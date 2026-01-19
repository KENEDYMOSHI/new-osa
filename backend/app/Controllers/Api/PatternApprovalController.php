<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PatternType;
use App\Models\InstrumentCategory;
use App\Models\InstrumentType;
use App\Models\PatternApplication;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;

class PatternApprovalController extends BaseController
{
    use ResponseTrait;

    protected $patternTypeModel;
    protected $instrumentCategoryModel;
    protected $instrumentTypeModel;
    protected $patternApplicationModel;

    public function __construct()
    {
        $this->patternTypeModel = new PatternType();
        $this->instrumentCategoryModel = new InstrumentCategory();
        $this->instrumentTypeModel = new InstrumentType();
        $this->patternApplicationModel = new PatternApplication();
    }

    /**
     * Get all active pattern types
     * GET /api/pattern-approval/pattern-types
     */
    public function getPatternTypes()
    {
        try {
            $patternTypes = $this->patternTypeModel
                ->where('is_active', 1)
                ->findAll();

            return $this->respond([
                'success' => true,
                'data' => $patternTypes
            ]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get all instrument categories
     * GET /api/pattern-approval/instrument-categories
     * Query Params: pattern_type_id (optional)
     */
    public function getInstrumentCategories()
    {
        try {
            $patternTypeId = $this->request->getGet('pattern_type_id');
            
            $query = $this->instrumentCategoryModel->where('is_active', 1);
            
            if ($patternTypeId) {
                $query->where('pattern_type_id', $patternTypeId);
            }
            
            $categories = $query->findAll();

            return $this->respond([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get instrument types by category
     * GET /api/pattern-approval/instrument-types/:categoryId
     */
    public function getInstrumentTypesByCategory($categoryId)
    {
        try {
            $instrumentTypes = $this->instrumentTypeModel
                ->where('category_id', $categoryId)
                ->where('is_active', 1)
                ->findAll();

            return $this->respond([
                'success' => true,
                'data' => $instrumentTypes
            ]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create new pattern application
     * POST /api/pattern-approval/applications
     */
    public function createApplication()
    {
        try {
            $userId = $this->request->user_id ?? null;
            
            if (!$userId) {
                return $this->fail('User not authenticated', ResponseInterface::HTTP_UNAUTHORIZED);
            }

            $data = $this->request->getJSON(true);
            
            if (!isset($data['pattern_type_id'])) {
                return $this->fail('Pattern type is required', ResponseInterface::HTTP_BAD_REQUEST);
            }

            $applicationData = [
                'user_id' => $userId,
                'pattern_type_id' => $data['pattern_type_id'],
                'status' => 'draft'
            ];

            $applicationId = $this->patternApplicationModel->insert($applicationData);

            if (!$applicationId) {
                return $this->fail('Failed to create application', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

            $application = $this->patternApplicationModel->getWithDetails($applicationId);

            return $this->respondCreated([
                'success' => true,
                'message' => 'Application created successfully',
                'data' => $application
            ]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get pattern application details
     * GET /api/pattern-approval/applications/:id
     */
    public function getApplication($id)
    {
        try {
            $userId = $this->request->user_id ?? null;
            
            if (!$userId) {
                return $this->fail('User not authenticated', ResponseInterface::HTTP_UNAUTHORIZED);
            }

            $application = $this->patternApplicationModel->find($id);

            if (!$application) {
                return $this->fail('Application not found', ResponseInterface::HTTP_NOT_FOUND);
            }

            // Verify ownership
            if ($application['user_id'] != $userId) {
                return $this->fail('Unauthorized access', ResponseInterface::HTTP_FORBIDDEN);
            }

            $applicationDetails = $this->patternApplicationModel->getWithDetails($id);
            $selectedInstruments = $this->patternApplicationModel->getSelectedInstruments($id);

            return $this->respond([
                'success' => true,
                'data' => [
                    'application' => $applicationDetails,
                    'instruments' => $selectedInstruments
                ]
            ]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get user's pattern applications
     * GET /api/pattern-approval/my-applications
     */
    public function getMyApplications()
    {
        try {
            $userId = $this->request->user_id ?? null;
            
            if (!$userId) {
                return $this->fail('User not authenticated', ResponseInterface::HTTP_UNAUTHORIZED);
            }

            $applications = $this->patternApplicationModel
                ->select('pattern_applications.*, pattern_types.name as pattern_type_name')
                ->join('pattern_types', 'pattern_types.id = pattern_applications.pattern_type_id')
                ->where('pattern_applications.user_id', $userId)
                ->orderBy('pattern_applications.created_at', 'DESC')
                ->findAll();

            return $this->respond([
                'success' => true,
                'data' => $applications
            ]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update pattern application
     * PUT /api/pattern-approval/applications/:id
     */
    public function updateApplication($id)
    {
        try {
            $userId = $this->request->user_id ?? null;
            
            if (!$userId) {
                return $this->fail('User not authenticated', ResponseInterface::HTTP_UNAUTHORIZED);
            }

            $application = $this->patternApplicationModel->find($id);

            if (!$application) {
                return $this->fail('Application not found', ResponseInterface::HTTP_NOT_FOUND);
            }

            // Verify ownership
            if ($application['user_id'] != $userId) {
                return $this->fail('Unauthorized access', ResponseInterface::HTTP_FORBIDDEN);
            }

            $data = $this->request->getJSON(true);
            $updateData = [];

            if (isset($data['pattern_type_id'])) {
                $updateData['pattern_type_id'] = $data['pattern_type_id'];
            }

            if (isset($data['status'])) {
                $updateData['status'] = $data['status'];
            }

            if (empty($updateData)) {
                return $this->fail('No data to update', ResponseInterface::HTTP_BAD_REQUEST);
            }

            $this->patternApplicationModel->update($id, $updateData);

            $updatedApplication = $this->patternApplicationModel->getWithDetails($id);

            return $this->respond([
                'success' => true,
                'message' => 'Application updated successfully',
                'data' => $updatedApplication
            ]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Add instrument to application
     * POST /api/pattern-approval/applications/:id/instruments
     */
    public function addInstrument($id)
    {
        try {
            $userId = $this->request->user_id ?? null;
            
            if (!$userId) {
                return $this->fail('User not authenticated', ResponseInterface::HTTP_UNAUTHORIZED);
            }

            $application = $this->patternApplicationModel->find($id);

            if (!$application) {
                return $this->fail('Application not found', ResponseInterface::HTTP_NOT_FOUND);
            }

            // Verify ownership
            if ($application['user_id'] != $userId) {
                return $this->fail('Unauthorized access', ResponseInterface::HTTP_FORBIDDEN);
            }

            $data = $this->request->getJSON(true);

            if (!isset($data['instrument_type_id'])) {
                return $this->fail('Instrument type is required', ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Check if instrument already exists
            $existing = $this->db->table('pattern_application_instruments')
                ->where('pattern_application_id', $id)
                ->where('instrument_type_id', $data['instrument_type_id'])
                ->get()
                ->getRow();

            if ($existing) {
                return $this->fail('Instrument already added', ResponseInterface::HTTP_CONFLICT);
            }

            // Prepare instrument data
            $instrumentData = [
                'pattern_application_id' => $id,
                'instrument_type_id'     => $data['instrument_type_id'],
                'brand_name'             => $data['brand_name'] ?? null,
                'make'                   => $data['make'] ?? null,
                'serial_number'          => $data['serial_number'] ?? null,
                'maximum_capacity'       => $data['maximum_capacity'] ?? null,
                'manual_calibration_doc' => $data['manual_calibration_doc'] ?? null,
                'specification_doc'      => $data['specification_doc'] ?? null,
                'created_at'             => date('Y-m-d H:i:s'),
                'updated_at'             => date('Y-m-d H:i:s'),
            ];

            $this->db->table('pattern_application_instruments')->insert($instrumentData);

            $selectedInstruments = $this->patternApplicationModel->getSelectedInstruments($id);

            return $this->respondCreated([
                'success' => true,
                'message' => 'Instrument added successfully',
                'data' => $selectedInstruments
            ]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove instrument from application
     * DELETE /api/pattern-approval/applications/:id/instruments/:instrumentTypeId
     */
    public function removeInstrument($id, $instrumentTypeId)
    {
        try {
            $userId = $this->request->user_id ?? null;
            
            if (!$userId) {
                return $this->fail('User not authenticated', ResponseInterface::HTTP_UNAUTHORIZED);
            }

            $application = $this->patternApplicationModel->find($id);

            if (!$application) {
                return $this->fail('Application not found', ResponseInterface::HTTP_NOT_FOUND);
            }

            // Verify ownership
            if ($application['user_id'] != $userId) {
                return $this->fail('Unauthorized access', ResponseInterface::HTTP_FORBIDDEN);
            }

            $this->patternApplicationModel->removeInstrument($id, $instrumentTypeId);

            $selectedInstruments = $this->patternApplicationModel->getSelectedInstruments($id);

            return $this->respond([
                'success' => true,
                'message' => 'Instrument removed successfully',
                'data' => $selectedInstruments
            ]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
