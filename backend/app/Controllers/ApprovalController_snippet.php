    public function getIssuedLicenses()
    {
        $requestKey = $this->request->getHeaderLine('X-API-KEY');
        
        if ($requestKey !== $this->apiKey) {
            return $this->failUnauthorized('Invalid API Key');
        }

        $db = \Config\Database::connect();
        
        $filters = [
            'name' => $this->request->getVar('name'),
            'region' => $this->request->getVar('region'),
            'license_type' => $this->request->getVar('license_type'),
            'year' => $this->request->getVar('year'),
            'dateRange' => $this->request->getVar('dateRange'),
            'company_name' => $this->request->getVar('company_name'),
        ];

        // Build query for licenses
        $builder = $db->table('licenses');
        $builder->select('licenses.*, 
                          practitioner_personal_infos.first_name,
                          practitioner_personal_infos.last_name,
                          practitioner_personal_infos.phone,
                          practitioner_business_infos.company_name as business_name');
        
        // Join with users to get UUID (if applicant_id is user_uuid, which it is in createLicense)
        // licenses.applicant_id stores the USER UUID.
        
        // Join with personal info
        $builder->join('practitioner_personal_infos', 'practitioner_personal_infos.user_uuid = licenses.applicant_id', 'left');
        
        // Join with business info
        $builder->join('practitioner_business_infos', 'practitioner_business_infos.user_uuid = licenses.applicant_id', 'left');
        
        // Apply filters
        if (!empty($filters['name'])) {
            $builder->groupStart();
            $builder->like('practitioner_personal_infos.first_name', $filters['name']);
            $builder->orLike('practitioner_personal_infos.last_name', $filters['name']);
            $builder->orLike('licenses.applicant_name', $filters['name']);
            $builder->groupEnd();
        }
        
        if (!empty($filters['region'])) {
            $builder->where('licenses.region', $filters['region']);
        }
        
        if (!empty($filters['license_type'])) {
            $builder->where('licenses.license_type', $filters['license_type']);
        }
        
        if (!empty($filters['company_name'])) {
            $builder->like('licenses.company_name', $filters['company_name']);
        }
        
        if (!empty($filters['year'])) {
            $builder->where('YEAR(licenses.created_at)', $filters['year']);
        }
        
        if (!empty($filters['dateRange'])) {
            $dates = explode(' - ', $filters['dateRange']);
            if (count($dates) == 2) {
                $builder->where('licenses.created_at >=', $dates[0]);
                $builder->where('licenses.created_at <=', $dates[1] . ' 23:59:59');
            }
        }
        
        $builder->orderBy('licenses.created_at', 'DESC');
        $licenses = $builder->get()->getResult();
        
        return $this->respond($licenses);
    }
