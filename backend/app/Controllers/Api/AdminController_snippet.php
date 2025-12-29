
    public function acceptDocument($id)
    {
        // Skip authentication in development mode if needed, but best to keep consistent
        if (ENVIRONMENT !== 'development') {
            $user = $this->getUserFromToken();
            if (!$user) {
                return $this->failUnauthorized('Invalid or expired token. Please login again.');
            }
        }

        $db = \Config\Database::connect();
        $builder = $db->table('license_application_attachments');
        
        $doc = $builder->where('id', $id)->get()->getRow();
        
        if (!$doc) {
            return $this->failNotFound('Document not found');
        }

        // Update status to Submitted (which corresponds to "Uploaded" / "Accepted" in the workflow)
        // Or "Approved" if that is preferred. Let's use "Submitted" to restore it to normal flow.
        // User said: "Applicant status changes to 'Uploaded' ... marked as Accepted"
        // In WMA-MIS, Submitted = Uploaded badge. 
        // If we want "Accepted" text, we might need a new status or use "Approved".
        // But "Approved" usually means the *application* is approved.
        // Let's use 'Submitted'.
        
        $data = [
            'status' => 'Submitted',
            'rejection_reason' => null, // Clear rejection reason
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $builder->where('id', $id);
        if (!$builder->update($data)) {
            return $this->fail('Failed to update document status');
        }

        return $this->respond(['message' => 'Document accepted successfully', 'status' => 'success']);
    }
