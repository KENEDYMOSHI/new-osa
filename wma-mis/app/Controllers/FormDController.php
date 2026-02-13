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
            'status' => $this->request->getGet('status') ?? 'Pending', // Default to Pending
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

         // Get Filters
         $filters = [
            'start_date' => $this->request->getGet('start_date'),
            'end_date' => $this->request->getGet('end_date'),
            'month' => $this->request->getGet('month'),
            // For Report, we want processed statuses unless specific filter is applied
            // But getRequests logic needs to handle 'processed' or array of statuses
            // For now, let's fetch all via simple where logic if model doesn't support 'not Pending'
            'status' => $this->request->getGet('status'), 
            'applicant_name' => $this->request->getGet('applicant_name'),
            'license_number' => $this->request->getGet('license_number'),
        ];

        // Custom fetching for reports (Approved or Rejected)
        // Adjusting getRequests or doing manual query here
        // Since getRequests filters by exact status if provided, we might need to modify model or do this:
        
        $builder = $this->formDModel->builder();
        $builder->select('form_d_requests.*, vessel_discharge.users.first_name as inspector_first_name, vessel_discharge.users.last_name as inspector_last_name');
        $builder->join('vessel_discharge.users', 'vessel_discharge.users.id = form_d_requests.inspector_id', 'left');
        $builder->whereIn('form_d_requests.status', ['Approved', 'Rejected']);
        
        if (!empty($filters['start_date'])) $builder->where("DATE(form_d_requests.created_at) >=", $filters['start_date']);
        if (!empty($filters['end_date'])) $builder->where("DATE(form_d_requests.created_at) <=", $filters['end_date']);
        if (!empty($filters['applicant_name'])) {
            $builder->groupStart()
                ->like('form_d_requests.practitioner_name', $filters['applicant_name'])
                ->orLike('form_d_requests.declarant_name', $filters['applicant_name'])
                ->groupEnd();
        }

        $data['requests'] = $builder->orderBy('form_d_requests.updated_at', 'DESC')->get()->getResultArray();
        
        // Generate tokens for report view as well
        $session = session();
        foreach ($data['requests'] as &$request) {
            $token = bin2hex(random_bytes(16));
            $session->set('print_token_' . $request['id'], $token);
            $request['print_token'] = $token;
        }

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

        // Use builder to join users table to get inspector name
        $request = $this->formDModel->builder()
            ->select('form_d_requests.*, vessel_discharge.users.first_name as inspector_first_name, vessel_discharge.users.last_name as inspector_last_name')
            ->join('vessel_discharge.users', 'vessel_discharge.users.id = form_d_requests.inspector_id', 'left')
            ->where('form_d_requests.id', $id)
            ->get()
            ->getRowArray();
        
        if (!$request) {
            return "Request not found";
        }

        $data['request'] = $request;
        return view('Pages/Osa/PrintFormDRequest', $data);
    }
}
