<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class DocumentApiController extends ResourceController
{
    protected $format = 'json';
    
    public function view($attachmentId)
    {
        // Check API key
        $apiKey = $this->request->getHeaderLine('X-API-KEY');
        if ($apiKey !== 'osa_approval_api_key_12345') {
            return $this->failUnauthorized('Invalid API key');
        }
        
        // Get database instance
        $db = \Config\Database::connect();
        
        // Get attachment from database
        $builder = $db->table('license_application_attachments');
        $attachment = $builder->where('id', $attachmentId)->get()->getRow();
        
        if (!$attachment) {
            return $this->failNotFound('Document not found');
        }
        
        // Check if file_content exists
        if (empty($attachment->file_content)) {
            return $this->failNotFound('Document content not available');
        }
        
        // Set appropriate headers
        $mimeType = $attachment->mime_type ?? 'application/pdf';
        $fileName = $attachment->original_name ?? 'document.pdf';
        
        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', 'inline; filename="' . $fileName . '"')
            ->setBody($attachment->file_content);
    }
    
    public function download($attachmentId)
    {
        // Check API key
        $apiKey = $this->request->getHeaderLine('X-API-KEY');
        if ($apiKey !== 'osa_approval_api_key_12345') {
            return $this->failUnauthorized('Invalid API key');
        }
        
        // Get database instance
        $db = \Config\Database::connect();
        
        // Get attachment from database
        $builder = $db->table('license_application_attachments');
        $attachment = $builder->where('id', $attachmentId)->get()->getRow();
        
        if (!$attachment) {
            return $this->failNotFound('Document not found');
        }
        
        // Check if file_content exists
        if (empty($attachment->file_content)) {
            return $this->failNotFound('Document content not available');
        }
        
        // Set appropriate headers for download
        $mimeType = $attachment->mime_type ?? 'application/pdf';
        $fileName = $attachment->original_name ?? 'document.pdf';
        
        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setBody($attachment->file_content);
    }
    
    public function returnDocument()
    {
        // Check API key
        $apiKey = $this->request->getHeaderLine('X-API-KEY');
        if ($apiKey !== 'osa_approval_api_key_12345') {
            return $this->failUnauthorized('Invalid API key');
        }

        $json = $this->request->getJSON();
        $documentId = $json->document_id ?? null;
        $rejectionReason = $json->rejection_reason ?? null;

        if (!$documentId || !$rejectionReason) {
            return $this->fail('Document ID and rejection reason are required');
        }
        
        try {
            $db = \Config\Database::connect();
            $builder = $db->table('license_application_attachments');
            
            $data = [
                'status' => 'Returned',
                'rejection_reason' => $rejectionReason,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $builder->where('id', $documentId);
            if (!$builder->update($data)) {
                return $this->fail('Failed to update document status in database');
            }

            // Notification Logic skipped for now as we don't have user context easily from API Key call
            // unles we fetch it from the doc.
            // Let's add basic notification if user_id exists on the attachment
            $doc = $db->table('license_application_attachments')->where('id', $documentId)->get()->getRow();
            if ($doc && !empty($doc->user_id)) {
                // Fetch Applicant Name and Application Number (Request Number)
                $appDetails = $db->table('license_applications')
                    ->select('license_applications.request_number, practitioner_personal_infos.first_name, practitioner_personal_infos.last_name')
                    ->join('license_users', 'license_users.id = license_applications.user_id', 'left')
                    ->join('practitioner_personal_infos', 'practitioner_personal_infos.user_uuid = license_users.uuid', 'left')
                    ->where('license_applications.id', $doc->application_id)
                    ->get()
                    ->getRow();

                $applicantName = $appDetails ? trim(($appDetails->first_name ?? '') . ' ' . ($appDetails->last_name ?? '')) : '';
                $requestNumber = $appDetails ? ($appDetails->request_number ?? 'N/A') : 'N/A';

                if (empty($applicantName)) {
                    $applicantName = "Ndugu";
                } else {
                    $applicantName = ucwords(strtolower($applicantName));
                }

                $documentNames = [
                    'tin' => 'Tax Payer Identification Number (TIN)',
                    'businessLicense' => 'Business License',
                    'incorporationCertificate' => 'Certificate of Incorporation/Registration',
                    'memorandum' => 'Memorandum and Articles of Association',
                    'ida' => 'Industrial Development Authority (IDA) Certificate',
                    'gmp' => 'Good Manufacturing Practice (GMP) Certificate',
                    'id' => 'Identity Card (National ID / Driver\'s License / Voter ID)',
                    'identity' => 'Identity Card (National ID / Driver\'s License / Voter ID)',
                    'tbs' => 'Tanzania Bureau of Standards (TBS) Certificate',
                    'tmda' => 'Tanzania Medicines and Medical Devices Authority (TMDA) Certificate'
                ];

                $docType = $doc->document_type;
                $fullDocName = isset($documentNames[$docType]) ? $documentNames[$docType] : $docType;

                $notifMessage = "Salamu,\n" .
                               "<b>{$applicantName}</b>\n\n" .
                               "Baada ya mapitio ya awali, imebainika kuwa hati ya <b>({$fullDocName})</b> uliyowasilisha inahitaji kufanyiwa marekebisho.\n\n" .
                               "Maelezo ya marekebisho:\n" .
                               "{$rejectionReason}\n\n" .
                               "Tafadhali fanya marekebisho kulingana na maelekezo yaliyotolewa kisha re-upload hati iliyorekebishwa ili kuendelea na hatua zinazofuata za maombi yako.\n\n" .
                               "<b>Maombi Na.:</b> {$requestNumber}\n\n" .
                               "Asante.\n\n" .
                               "Weights and Measures Agency (WMA)\n" .
                               "Vipimo House, Chief Chemist Street\n" .
                               "P.O. Box 2014, Dodoma – Tanzania\n" .
                               "info@wma.go.tz";

                 $notifBuilder = $db->table('notifications');
                 $uuid = md5(uniqid(rand(), true));
                 $notifData = [
                    'id' => $uuid,
                    'user_id' => $doc->user_id,
                    'title' => 'Document Returned',
                    'message' => $notifMessage,
                    'type' => 'document_returned',
                    'related_entity_id' => $doc->application_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $notifBuilder->insert($notifData);
            }

        } catch (\Exception $e) {
            return $this->fail('Server Error: ' . $e->getMessage());
        }

        return $this->respond(['message' => 'Document returned successfully']);
    }
    
    /**
     * Accept a resubmitted document
     * POST /api/documents/:id/accept
     */
    public function acceptDocument($documentId = null)
    {
        // Check API key
        $apiKey = $this->request->getHeaderLine('X-API-KEY');
        if ($apiKey !== 'osa_approval_api_key_12345') {
            return $this->failUnauthorized('Invalid API key');
        }

        if (!$documentId) {
            return $this->fail('Document ID is required');
        }

        try {
            $db = \Config\Database::connect();
            $builder = $db->table('license_application_attachments');
            
            // Get document
            $document = $builder->where('id', $documentId)->get()->getRow();
            
            if (!$document) {
                return $this->failNotFound('Document not found');
            }

            // Validate status is Resubmitted
            if ($document->status !== 'Resubmitted') {
                return $this->fail('Only resubmitted documents can be accepted. Current status: ' . $document->status);
            }

            // Update status to Uploaded
            $data = [
                'status' => 'Uploaded',
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $builder->where('id', $documentId);
            if (!$builder->update($data)) {
                return $this->fail('Failed to update document status');
            }

            // Log in audit trail
            $auditBuilder = $db->table('document_audit_trails');
            $auditData = [
                'id' => md5(uniqid(rand(), true)),
                'document_id' => $documentId,
                'action' => 'Accepted',
                'performed_by' => 'WMA-MIS Approver',
                'old_status' => 'Resubmitted',
                'new_status' => 'Uploaded',
                'comments' => 'Document accepted by approver',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $auditBuilder->insert($auditData);

            return $this->respond([
                'success' => true,
                'message' => 'Document accepted successfully'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error accepting document: ' . $e->getMessage());
            return $this->fail('Server Error: ' . $e->getMessage());
        }
    }
}
