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
        // 1. Check App Status
        $app = $this->initialAppModel->find($appId);
        $appStatus = $appStatus ?? 'Draft'; // Default

        // 2. Compute Permission (Can we edit/upload?)
        // If app is Draft, yes.
        // If app is Submitted/UnderReview/Approved, NO (unless missing docs scenario, handled below).
        // If app is Returned, only if doc specific status is Draft or Returned (or if missing).
        
        $canUpload = false;
        if ($appStatus === 'Draft') {
            $canUpload = true;
        } elseif ($appStatus === 'Returned') {
            // Check specific document status if it exists
            $existing = $this->attachmentModel->where('application_id', $appId)
                                              ->where('document_type', $documentType)
                                              ->orderBy('created_at', 'DESC')
                                              ->first();
            if (!$existing || $existing->status === 'Returned' || $existing->status === 'Draft') {
                 $canUpload = true;
            }
        } elseif ($appStatus === 'Submitted' || $appStatus === 'Under Review') {
             // Allow upload ONLY if document is MISSING (Rule 5)
             $existing = $this->attachmentModel->where('application_id', $appId)
                                               ->where('document_type', $documentType)
                                               ->countAllResults();
             if ($existing === 0) {
                 $canUpload = true;
             }
        }

        if (!$canUpload) {
            throw new \Exception("Upload not allowed for this document in current application status.");
        }

        // 3. Handle File Upload
        if (!$file->isValid()) {
             throw new \Exception($file->getErrorString());
        }

        $newName = $file->getRandomName();
        // Assuming writable/uploads/licenses is the path
        $file->move(WRITEPATH . 'uploads/licenses', $newName);

        // 4. Save to DB
        $data = [
            'id' => (string) \CodeIgniter\Uuid::uuid4(),
            'user_id' => $userId,
            'application_id' => $appId, // Link immediately if app exists
            'document_type' => $documentType,
            'file_path' => 'uploads/licenses/' . $newName,
            'original_name' => $file->getClientName(),
            'mime_type' => $file->getClientMimeType(),
            'status' => 'Draft', // Always Draft initially until saved/submitted
        ];
        
        $this->attachmentModel->insert($data);
        return $data;
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
