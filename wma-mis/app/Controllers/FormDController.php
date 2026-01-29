<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FormDRequestModel;
use App\Models\UsersModel; 

class FormDController extends BaseController
{
    protected $formDModel;
    protected $userModel;

    public function __construct()
    {
        $this->formDModel = new FormDRequestModel();
        // Assuming UsersModel exists and handles user data. 
        // If not, we might need to use UserLoginModel or similar depending on auth setup.
        // For now using generic UsersModel or referencing the table directly if needed.
        // Based on previous ls, UsersModel exists.
        $this->userModel = new UsersModel(); 
    }

    public function requestedFormD()
    {
        $data['page'] = [
            "title" => "Requested Form D",
            "heading" => "Requested Form D",
        ];

        $data['user'] = auth()->user();

        // Get Filters
        $filters = [
            'start_date' => $this->request->getGet('start_date'),
            'end_date' => $this->request->getGet('end_date'),
            'month' => $this->request->getGet('month'),
            'status' => $this->request->getGet('status'),
            'applicant_name' => $this->request->getGet('applicant_name'),
            'license_number' => $this->request->getGet('license_number'),
            'seal_number' => $this->request->getGet('seal_number'),
            'inspector_id' => $this->request->getGet('inspector_id'),
        ];

        $data['filters'] = $filters;
        $data['requests'] = $this->formDModel->getRequests($filters);

        // Generate print tokens for each request (for iframe access)
        $session = session();
        foreach ($data['requests'] as &$request) {
            $token = bin2hex(random_bytes(16));
            $session->set('print_token_' . $request['id'], $token);
            $request['print_token'] = $token;
        }

        // Fetch Inspectors (Users with role 'officer' or specific group)
        // Adjust logic based on actual role management. 
        // Assuming getting all users for now or filtering by group if method exists.
        // If Model has `getUsersByGroup` or similar, use it. 
        // Fallback to fetching all for demo or specific query.
        $db = \Config\Database::connect();
        // Assuming auth_groups_users maps users to groups (standard CI Shield/MythAuth)
        // Or a simple 'role' column in users table.
        // Let's try to fetch users who might be inspectors.
        // For safety, I'll fetch all users for the dropdown for now, or check if 'officer' group exists.
        $data['inspectors'] = $this->userModel->findAll(); 

        return view('Pages/Osa/RequestedFormD', $data);
    }

    public function reportFormDRequest()
    {
        $data['page'] = [
            "title" => "Report Form D Request",
            "heading" => "Report Form D Request",
        ];

        $data['user'] = auth()->user();
        return view('Pages/Osa/ReportFormDRequest', $data);
    }

    public function processRequest()
    {
        $id = $this->request->getPost('id');
        $decision = $this->request->getPost('decision');
        $comments = $this->request->getPost('comments');

        if (!$id || !$decision) {
            return redirect()->back()->with('error', 'Missing required information.');
        }

        if ($decision === 'approve') {
            $inspectorId = $this->request->getPost('inspector_id');
            $inspectionDate = $this->request->getPost('inspection_date');

            if (!$inspectorId || !$inspectionDate) {
                return redirect()->back()->with('error', 'Please select an inspector and inspection date.');
            }

            $this->formDModel->update($id, [
                'status' => 'Approved',
                'inspector_id' => $inspectorId,
                'verification_date' => $inspectionDate,
                'assignment_notes' => $comments
            ]);

            return redirect()->back()->with('success', 'Request approved and inspector assigned successfully.');
        } 
        elseif ($decision === 'reject') {
            $rejectionReason = $this->request->getPost('rejection_reason');

            if (!$rejectionReason) {
                return redirect()->back()->with('error', 'Please provide a rejection reason.');
            }

            $this->formDModel->update($id, [
                'status' => 'Rejected',
                'rejection_reason' => $rejectionReason,
                'assignment_notes' => $comments
            ]);

            return redirect()->back()->with('success', 'Request rejected successfully.');
        }

        return redirect()->back()->with('error', 'Invalid decision selected.');
    }

    public function approveRequest()
    {
        $id = $this->request->getPost('id');
        $inspectorId = $this->request->getPost('inspector_id');
        $inspectionDate = $this->request->getPost('inspection_date');
        $notes = $this->request->getPost('assignment_notes');

        if ($id && $inspectorId && $inspectionDate) {
            $this->formDModel->update($id, [
                'status' => 'Approved',
                'inspector_id' => $inspectorId,
                'verification_date' => $inspectionDate, // Mapped to verification_date
                'inspection_report' => $notes // Mapped to inspection_report
            ]);
            return redirect()->back()->with('success', 'Request approved and inspector assigned successfully.');
        }

        return redirect()->back()->with('error', 'Failed to approve request. Missing information.');
    }

    public function rejectRequest()
    {
        $id = $this->request->getPost('id');
        $reason = $this->request->getPost('rejection_reason');

        if ($id && $reason) {
            $this->formDModel->update($id, [
                'status' => 'Rejected',
                'rejection_reason' => $reason
            ]);
            return redirect()->back()->with('success', 'Request rejected successfully.');
        }
        
        return redirect()->back()->with('error', 'Failed to reject request. Please provide a reason.');
    }

    public function printRequest($id)
    {
        // Validate token from session
        $session = session();
        $token = $this->request->getGet('token');
        $storedToken = $session->get('print_token_' . $id);

        if (!$token || $token !== $storedToken) {
            // If no valid token, check if user is authenticated
            if (!auth()->loggedIn()) {
                return "Unauthorized access";
            }
        }

        $request = $this->formDModel->find($id);
        
        if (!$request) {
            return "Request not found";
        }

        $data['request'] = $request;
        return view('Pages/Osa/PrintFormDRequest', $data);
    }
}
