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



    private function getInstrumentCategoryName($instrumentTypeId)
    {
        $type = $this->instrumentTypeModel->find($instrumentTypeId);
        if (!$type) return null;
        
        $category = $this->instrumentCategoryModel->find($type['category_id']);
        return $category ? $category['name'] : null;
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
            if (empty($data)) {
                $data = $this->request->getPost();
            }

            if (!isset($data['instrument_type_id'])) {
                return $this->fail('Instrument type is required', ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Handle File Uploads
            $docFields = ['manual_calibration_doc', 'specification_doc', 'other_doc', 'type_approval_doc'];
            foreach ($docFields as $field) {
                $filePath = $this->handleFileUpload($field);
                if ($filePath) {
                    $data[$field] = $filePath;
                }
            }

            $categoryName = $this->getInstrumentCategoryName($data['instrument_type_id']);
            $isWeighingInstrument = false;
            $isCapacityMeasure = false;
            
            // Check if it is a weighing instrument based on category name or other logic
            // Assuming 'Weighing Instrument' pattern type categories map to it
            // Or simpler check: if extra fields 'scale_type' etc are present
            
            // Better check: Get pattern type name
            $patternType = $this->patternTypeModel->find($application['pattern_type_id']);
            $isMeterInstrument = false; // Initialize

            if ($patternType) {
                if (stripos($patternType['name'], 'Weighing') !== false) {
                    $isWeighingInstrument = true;
                } elseif (stripos($patternType['name'], 'Capacity Measure') !== false) {
                    $isCapacityMeasure = true;
                } elseif (stripos($patternType['name'], 'Meter') !== false) {
                    $isMeterInstrument = true;
                }
            }

            if ($isWeighingInstrument) {
                 // Weighing Instrument Logic
                 $weighingModel = new \App\Models\WeighingInstrumentModel();
                 
                // Prepare instrument data
                $instrumentData = [
                    'pattern_application_id' => $id,
                    'instrument_type_id'     => $data['instrument_type_id'],
                    'brand_name'             => $data['brand_name'] ?? null,
                    'make'                   => $data['make'] ?? null,
                    'quantity'               => $data['quantity'] ?? 1,
                    'serial_numbers'         => $data['serial_number'] ?? null,
                    'accuracy_class'         => $data['accuracy_class'] ?? null,
                    'maximum_capacity'       => $data['maximum_capacity'] ?? null,
                    'manual_calibration_doc' => $data['manual_calibration_doc'] ?? null,
                    'specification_doc'      => $data['specification_doc'] ?? null,
                    'other_doc'              => $data['other_doc'] ?? null,
                    'scale_type'             => $data['scale_type'] ?? null,
                    'instrument_use'         => $data['instrument_use'] ?? null,
                    'value_e'                => $data['value_e'] ?? null,
                    'value_d'                => $data['value_d'] ?? null,
                    'application_fee'        => $data['application_fee'] ?? 0.00,
                    'pattern_fee'            => $data['pattern_fee'] ?? 0.00,
                    'created_at'             => date('Y-m-d H:i:s'),
                    'updated_at'             => date('Y-m-d H:i:s'),
                ];
                
                $weighingModel->insert($instrumentData);

            } elseif ($isCapacityMeasure) {
                 // Capacity Measure Logic
                 $capacityModel = new \App\Models\CapacityMeasureInstrumentModel();
                 
                 $instrumentData = [
                    'pattern_application_id' => $id,
                    'instrument_type_id'     => $data['instrument_type_id'],
                    'brand_name'             => $data['brand_name'] ?? null,
                    'manufacturer'           => $data['make'] ?? null, 
                    'meter_model'            => $data['meter_model'] ?? null,
                    'quantity'               => $data['quantity'] ?? 1,
                    'serial_numbers'         => isset($data['serial_numbers']) && is_array($data['serial_numbers']) 
                                                ? implode(',', $data['serial_numbers']) 
                                                : ($data['serial_numbers'] ?? null),
                    
                    'material_construction'    => $data['material_construction'] ?? null,
                    'year_manufacture'         => $data['year_manufacture'] ?? null,
                    'measurement_unit'         => $data['measurement_unit'] ?? null,
                    'nominal_capacity'         => $data['nominal_capacity'] ?? null,
                    'max_permissible_error'    => $data['max_permissible_error'] ?? null,
                    'temperature_range'        => $data['temperature_range'] ?? null,
                    'intended_liquid'          => $data['intended_liquid'] ?? null,
                    'has_seal_arrangement'     => $data['has_seal_arrangement'] ?? null,
                    'has_adjustment_mechanism' => $data['has_adjustment_mechanism'] ?? null,
                    'has_gauge_glass'          => $data['has_gauge_glass'] ?? null,
                    'other_doc'                => $data['other_doc'] ?? null,
                    'type_approval_doc'        => $data['type_approval_doc'] ?? null,
                    'application_fee'          => $data['application_fee'] ?? 0.00,
                    'pattern_fee'              => $data['pattern_fee'] ?? 0.00,

                    'created_at'             => date('Y-m-d H:i:s'),
                    'updated_at'             => date('Y-m-d H:i:s'),
                 ];

                 $capacityModel->insert($instrumentData);

            } elseif ($isMeterInstrument) {
                // Meter Instrument Logic (Flow Meters, etc)
                // Direct DB insert as no model exists yet
                
                $instrumentData = [
                    'application_id'         => $id, // Uses application_id not pattern_application_id
                    'instrument_type_id'     => $data['instrument_type_id'],
                    'brand_name'             => $data['brand_name'] ?? null,
                    'manufacturer'           => $data['make'] ?? null,
                    'meter_model'            => $data['meter_model'] ?? null,
                    'quantity'               => $data['quantity'] ?? 1,
                    'serial_numbers'         => $data['serial_number'] ?? null,
                    'application_fee'        => $data['application_fee'] ?? 0.00,

                    // Meter Specifics
                    'nominal_flow_rate'       => $data['nominal_flow_rate'] ?? null,
                    'meter_class'             => $data['meter_class'] ?? null,
                    'ratio'                   => $data['ratio'] ?? null,
                    'max_admissible_pressure' => $data['max_admissible_pressure'] ?? null,
                    'max_temperature'         => $data['max_temperature'] ?? null,
                    'meter_size_dn'           => $data['meter_size_dn'] ?? null,
                    'diameter'                => $data['diameter'] ?? null,
                    'position_hv_type'        => $data['position_hv_type'] ?? null,
                    'sealing_mechanism_type'  => $data['sealing_mechanism_type'] ?? null,
                    'flow_direction_type'     => $data['flow_direction_type'] ?? null,
                    
                    // Electrical
                    'meter_type'              => $data['meter_type'] ?? null,
                    'nominal_voltage'         => $data['nominal_voltage'] ?? null,
                    'nominal_frequency'       => $data['nominal_frequency'] ?? null,
                    'maximum_current'         => $data['maximum_current'] ?? null,
                    'transitional_current'    => $data['transitional_current'] ?? null,
                    'minimum_current'         => $data['minimum_current'] ?? null,
                    'starting_current'        => $data['starting_current'] ?? null,
                    'connection_type'         => $data['connection_type'] ?? null,
                    'connection_mode'         => $data['connection_mode'] ?? null,
                    'alternative_connection_mode' => $data['alternative_connection_mode'] ?? null,
                    'energy_flow_direction'   => $data['energy_flow_direction'] ?? null,
                    'meter_constant'          => $data['meter_constant'] ?? null,
                    'clock_frequency'         => $data['clock_frequency'] ?? null,
                    'environment'             => $data['environment'] ?? null,
                    'ip_rating'               => $data['ip_rating'] ?? null,
                    'terminal_arrangement'    => $data['terminal_arrangement'] ?? null,
                    'insulation_protection_class' => $data['insulation_protection_class'] ?? null,
                    'temperature_lower'       => $data['temperature_lower'] ?? null,
                    'temperature_upper'       => $data['temperature_upper'] ?? null,
                    'humidity_class'          => $data['humidity_class'] ?? null,
                    'hardware_version'        => $data['hardware_version'] ?? null,
                    'software_version'        => $data['software_version'] ?? null,
                    'remarks'                 => $data['remarks'] ?? null,
                    'test_voltage'            => $data['test_voltage'] ?? null,
                    'test_frequency'          => $data['test_frequency'] ?? null,
                    'test_connection_mode'    => $data['test_connection_mode'] ?? null,
                    'test_remarks'            => $data['test_remarks'] ?? null,

                    'other_doc'              => $data['other_doc'] ?? null,
                    'application_fee'        => $data['application_fee'] ?? 0.00,
                    'pattern_fee'            => $data['pattern_fee'] ?? 0.00,
                    
                    'created_at'             => date('Y-m-d H:i:s'),
                    'updated_at'             => date('Y-m-d H:i:s'),
                ];
                
                $this->db->table('meter_instruments')->insert($instrumentData);

            } else {
                // Standard Instrument Logic
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
                    'other_doc'              => $data['other_doc'] ?? null,
                    'application_fee'        => $data['application_fee'] ?? 0.00,
                    'created_at'             => date('Y-m-d H:i:s'),
                    'updated_at'             => date('Y-m-d H:i:s'),
                ];

                $this->db->table('pattern_application_instruments')->insert($instrumentData);
            }

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

    private function handleFileUpload($field)
    {
        $file = $this->request->getFile($field);
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads', $newName);
            return 'uploads/' . $newName;
        }
        return null;
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
            
            // Try to remove from standard instruments
            $this->patternApplicationModel->removeInstrument($id, $instrumentTypeId);
            
            // Try to remove from weighing instruments
            $weighingModel = new \App\Models\WeighingInstrumentModel();
            $weighingModel->where('pattern_application_id', $id)
                          ->where('instrument_type_id', $instrumentTypeId)
                          ->delete();

            // Try to remove from capacity measure instruments
            $capacityModel = new \App\Models\CapacityMeasureInstrumentModel();
            $capacityModel->where('pattern_application_id', $id)
                          ->where('instrument_type_id', $instrumentTypeId)
                          ->delete();

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
