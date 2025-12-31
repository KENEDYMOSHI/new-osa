<?php

namespace App\Services;

use App\Models\LicenseApplicationAttachmentModel;
use App\Models\InitialApplicationModel;

class DocumentService
{
    protected $attachmentModel;
    protected $initialAppModel;

    public function __construct()
    {
        $this->attachmentModel = new LicenseApplicationAttachmentModel();
        $this->initialAppModel = new InitialApplicationModel();
    }

    /**
     * Get documents for an application with computed permissions.
     */
    public function getApplicationDocuments($appId, $userId)
    {
        // 1. Fetch Application Status
        $app = $this->initialAppModel->find($appId);
        $appStatus = $appStatus ?? 'Draft'; // Default to Draft if new

        // 2. Fetch Linked Documents
        $docs = $this->attachmentModel->where('application_id', $appId)
                                      ->orWhere('user_id', $userId) // Include unlinked drafts for this user if in Draft mode
                                      ->findAll();

        // 3. Filter and Compute Permissions
        $processedDocs = [];
        $latestDocs = [];

        // Simple deduplication logic (similar to Controller) to get latest per type
        foreach ($docs as $doc) {
             // Only include if linked to this app OR (if app is Draft, include user's unlinked docs)
             if ($doc->application_id === $appId || ($appStatus === 'Draft' && $doc->application_id === null)) {
                 if (!isset($latestDocs[$doc->document_type]) || strtotime($doc->created_at) > strtotime($latestDocs[$doc->document_type]->created_at)) {
                     $latestDocs[$doc->document_type] = $doc;
                 }
             }
        }

        foreach ($latestDocs as $doc) {
            unset($doc->file_content); // Optimization
            $doc->actions = $this->computeActions($appStatus, $doc->status);
            $processedDocs[] = $doc;
        }

        return $processedDocs;
    }

    /**
     * Compute allowed actions based on App Status and Document Status.
     */
    protected function computeActions($appStatus, $docStatus)
    {
        $actions = ['view' => true, 'edit' => false, 'delete' => false, 'save' => false];

        if ($appStatus === 'Draft') {
            // In Draft, everything is editable
            $actions['edit'] = true;
            $actions['delete'] = true;
        } elseif ($appStatus === 'Submitted' || $appStatus === 'Under Review' || $appStatus === 'Approved') {
            // Locked states
            // Exception: If App is Approved, maybe no view? But usually View is fine.
            // Strict user request: "Only one action is allowed: View"
        } elseif ($appStatus === 'Returned') {
            // Partial Unlock
            if ($docStatus === 'Returned' || $docStatus === 'Draft') { // Draft implies it was added/replaced during correction
                $actions['edit'] = true; // Replace implies Edit
                $actions['save'] = true; // User specifically asked for 'Save' on unlocked docs
            }
        }

        return $actions;
    }

    /**
     * Lock all documents when application is submitted.
     */
    public function lockDocumentsOnSubmit($appId)
    {
        $this->attachmentModel->where('application_id', $appId)
                              ->where('status', 'Draft')
                              ->set(['status' => 'Submitted'])
                              ->update();
    }

    public function uploadDocument($appId, $userId, $documentType, $file)
    {
        // CRITICAL: Ensure application_id is never null
        if (empty($appId)) {
            throw new \Exception("Application ID is required for document upload.");
        }
        
        // 1. Check App Status
        $app = $this->initialAppModel->find($appId);
        $appStatus = $app['status'] ?? 'Draft'; // Fixed: use $app['status'] instead of undefined $appStatus

        // 2. Check for existing document to preserve category
        $existingDoc = $this->attachmentModel->where('application_id', $appId)
                                              ->where('document_type', $documentType)
                                              ->orderBy('created_at', 'DESC')
                                              ->first();

        // 3. Compute Permission (Can we edit/upload?)
        $canUpload = false;
        if ($appStatus === 'Draft') {
            $canUpload = true;
        } elseif ($appStatus === 'Returned') {
            // Check specific document status if it exists
            if (!$existingDoc || $existingDoc->status === 'Returned' || $existingDoc->status === 'Draft') {
                 $canUpload = true;
            }
        } elseif ($appStatus === 'Submitted' || $appStatus === 'Under Review') {
             // Allow upload ONLY if document is MISSING (Rule 5)
             if (!$existingDoc) {
                 $canUpload = true;
             }
        }

        if (!$canUpload) {
            throw new \Exception("Upload not allowed for this document in current application status.");
        }

        // 4. Handle File Upload
        if (!$file->isValid()) {
             throw new \Exception($file->getErrorString());
        }

        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/licenses', $newName);

        // 5. Determine category - PRESERVE existing category if document exists
        $category = null;
        
        if ($existingDoc && !empty($existingDoc->category)) {
            // PRESERVE: Use existing category to keep document in same section
            $category = $existingDoc->category;
        } else {
            // NEW DOCUMENT: Determine category based on document type
            $qualificationTypes = [
                'psle', 'csee', 'acsee', 'veta', 'nta4', 'nta5', 'nta6',
                'specialized', 'bachelor', 'diploma', 'degree', 'master', 'phd',
                'cv', 'certificate', 'professional_certificate'
            ];
            
            $category = in_array(strtolower($documentType), $qualificationTypes) ? 'qualification' : 'attachment';
        }

        // 6. Save to DB
        // UPDATE if exists to preserve ID (Fixes 404 in WMA-MIS when viewing re-uploaded docs)
        if ($existingDoc) {
             $data = [
                'user_id' => $userId, // Ensure user ownership stays correct
                // application_id - cannot change
                // document_type - matches check
                // category - preserved above
                'file_path' => 'uploads/licenses/' . $newName,
                'original_name' => $file->getClientName(),
                'mime_type' => $file->getClientMimeType(),
                'status' => 'Submitted', // Change status to Submitted (or 'Resubmitted'?) User usually expects 'Resubmitted' or just cleared 'Returned'.
                                         // If we set 'Submitted', it clears 'Returned'.
                                         // Let's use 'Draft' or 'Submitted' depending on flow. Logic below says 'Draft'.
                                         // Let's stick to 'Draft' so they can review before submitting app, OR 'Resubmitted' if it was returned.
                                         // If it was 'Returned', updating it should probably make it 'Resubmitted' or 'Draft'?
                                         // Based on current logic `status => 'Draft'`, let's stick to 'Draft' or 'Resubmitted' if we want to signal change.
                                         // However, existing logic lines 165 says 'Draft'. Let's keep 'Draft' or 'Resubmitted' if previous was Returned.
             ];
             
             // If previous status was Returned, auto-switch to Resubmitted?
             // Or keep Draft until they hit "Submit" button?
             // Frontend usually requires explicit submit button? 
             // The frontend flow for re-upload for "Returned" items usually auto-submits or asks for submit?
             // Current insert logic sets 'Draft'. Let's keep it 'Draft' for consistency, or 'Resubmitted' if improving.
             // Let's allow 'Resubmitted' if it was 'Returned' to save a step, OR sticking to 'Draft'.
             // SAFEST: Update everything.
             
             $data['status'] = 'Resubmitted'; // Auto-resubmit for returned docs so Admin sees the change
             $data['rejection_reason'] = null; // Clear rejection reason
             
             // If it wasn't returned (e.g. Draft overwrite), keep Draft
             if ($existingDoc->status === 'Draft') {
                 $data['status'] = 'Draft';
             }

             $this->attachmentModel->update($existingDoc->id, $data);
             
             // Return existing doc with updated data
             $existingDoc = (array)$existingDoc; // convert to array if object
             return array_merge($existingDoc, $data);
        } else {
            // INSERT NEW
            $data = [
                'id' => (string) \CodeIgniter\Uuid::uuid4(),
                'user_id' => $userId,
                'application_id' => $appId,
                'document_type' => $documentType,
                'category' => $category,
                'file_path' => 'uploads/licenses/' . $newName,
                'original_name' => $file->getClientName(),
                'mime_type' => $file->getClientMimeType(),
                'status' => 'Draft',
            ];
            
            $this->attachmentModel->insert($data);
            return $data;
        }
    }

    public function deleteDocument($appId, $docId)
    {
        $doc = $this->attachmentModel->find($docId);
        if (!$doc) throw new \Exception("Document not found");

        $app = $this->initialAppModel->find($appId);
        $appStatus = $appStatus ?? 'Draft';

        $actions = $this->computeActions($appStatus, $doc->status);
        if (!$actions['delete']) {
            throw new \Exception("Deletion not allowed");
        }

        $this->attachmentModel->delete($docId);
    }

    public function submitDocument($appId, $docId)
    {
         $doc = $this->attachmentModel->find($docId);
         if (!$doc) throw new \Exception("Document not found");
         
         $app = $this->initialAppModel->find($appId);
         $appStatus = $appStatus ?? 'Draft';

         $actions = $this->computeActions($appStatus, $doc->status);
         if (!$actions['save']) {
             throw new \Exception("Submission not allowed");
         }

         $this->attachmentModel->update($docId, ['status' => 'Resubmitted']);
         // Optionally check if all documents are resubmitted to change App Status?
         // User request: "The Save button must allow submission of only the unlocked or missing document."
    }
}
