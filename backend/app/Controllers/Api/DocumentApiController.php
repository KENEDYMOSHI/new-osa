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
                 $notifBuilder = $db->table('notifications');
                 $uuid = md5(uniqid(rand(), true));
                 $notifData = [
                    'id' => $uuid,
                    'user_id' => $doc->user_id,
                    'title' => 'Document Returned',
                    'message' => "Your document '{$doc->document_type}' was returned. Reason: {$rejectionReason}",
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
}
