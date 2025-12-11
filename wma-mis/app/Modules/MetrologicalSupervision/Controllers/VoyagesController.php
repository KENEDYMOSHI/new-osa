<?php

namespace App\Modules\MetrologicalSupervision\Controllers;

use App\Controllers\BaseController;
use App\Modules\MetrologicalSupervision\Models\VoyageModel;
use App\Modules\MetrologicalSupervision\Models\VesselModel;
use App\Modules\MetrologicalSupervision\Models\VesselTankModel;

class VoyagesController extends BaseController
{
    protected $voyageModel;
    protected $vesselsModel;
    protected $tankModel;

    public function __construct()
    {
        $this->voyageModel = new VoyageModel();
        $this->vesselsModel = new VesselModel();
        $this->tankModel = new VesselTankModel();
    }

    public function index()
    {
        try {
            $data['vessels'] = $this->vesselsModel->orderBy('vesselName', 'ASC')->findAll();

            $db = \Config\Database::connect();
            $data['ports'] = $db->table('metro_port')->orderBy('portName', 'ASC')->get()->getResultArray();
            $data['berths'] = $db->table('metro_berths')->orderBy('berthName', 'ASC')->get()->getResultArray();

            return view('App\Modules\MetrologicalSupervision\Views\Voyages\Voyages', $data);
        } catch (\Exception $e) {
            return view('errors/html/error_general', ['heading' => 'Error', 'message' => $e->getMessage()]);
        }
    }

    public function getVoyages($vesselId)
    {
        try {
            $voyages = $this->voyageModel
                ->select('metro_voyage.*, loading.portName as loadingPortName, arrival.portName as arrivalPortName')
                ->join('metro_port as loading', 'loading.id = metro_voyage.loadingPort', 'left')
                ->join('metro_port as arrival', 'arrival.id = metro_voyage.arrivalPort', 'left')
                ->where('vesselId', $vesselId)
                ->orderBy('createdAt', 'DESC')
                ->findAll();

            $html = $this->renderVoyagesRows($voyages);

            return $this->response->setJSON(['status' => 1, 'html' => $html, 'token' => csrf_hash()]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 0, 'msg' => $e->getMessage(), 'token' => csrf_hash()]);
        }
    }

    public function save()
    {
        try {
            $rules = [
                'vesselId' => 'required',
                'arrivalPort' => 'required',
                'arrivalBerth' => 'required',
                'arrivalDate' => 'required',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => 0,
                    'errors' => $this->validator->getErrors(),
                    'token' => csrf_hash()
                ]);
            }

            $vesselId = $this->request->getPost('vesselId');
            $existingVoyageId = $this->request->getPost('voyageId'); // Hidden input from form

            if ($existingVoyageId) {
                // UPDATE
                $data = [
                    'vesselExperienceFactor' => $this->request->getPost('vesselExperienceFactor'),
                    'loadingPort' => $this->request->getPost('loadingPort') ?: null,
                    'arrivalPort' => $this->request->getPost('arrivalPort'),
                    'arrivalBerth' => $this->request->getPost('arrivalBerth'),
                    'loadingDate' => $this->request->getPost('loadingDate') ?: null,
                    'arrivalDate' => $this->request->getPost('arrivalDate'),
                ];

                if ($this->voyageModel->update($existingVoyageId, $data)) {
                    // Success (fall through to fetch list)
                } else {
                    return $this->response->setJSON(['status' => 0, 'msg' => 'Failed to update voyage', 'token' => csrf_hash()]);
                }
            } else {
                // INSERT
                // Voyage ID: VOYAGE + YmdHis + rand
                $voyageId = 'VOYAGE' . date('YmdHis') . rand(1000, 9999);

                // Voyage Number: YVG-NO-XXXX (Per Vessel)
                $lastVoyage = $this->voyageModel->where('vesselId', $vesselId)
                    ->orderBy('createdAt', 'DESC')
                    ->first();
                $nextNum = 1;
                if ($lastVoyage) {
                    $lastNumStr = $lastVoyage->voyageNumber ?? '';
                    if (preg_match('/YVG-NO-(\d+)/', $lastNumStr, $matches)) {
                        $nextNum = intval($matches[1]) + 1;
                    }
                }
                $voyageNumber = sprintf('YVG-NO-%04d', $nextNum);

                $data = [
                    'voyageId' => $voyageId,
                    'voyageNumber' => $voyageNumber,
                    'vesselId' => $vesselId,
                    'vesselExperienceFactor' => $this->request->getPost('vesselExperienceFactor'),
                    'loadingPort' => $this->request->getPost('loadingPort') ?: null,
                    'arrivalPort' => $this->request->getPost('arrivalPort'),
                    'arrivalBerth' => $this->request->getPost('arrivalBerth'),
                    'loadingDate' => $this->request->getPost('loadingDate') ?: null,
                    'arrivalDate' => $this->request->getPost('arrivalDate'),
                ];

                if (!$this->voyageModel->insert($data)) {
                    return $this->response->setJSON(['status' => 0, 'msg' => 'Failed to save voyage', 'token' => csrf_hash()]);
                }
            }

            // Fetch updated list (shared for both)
            $voyages = $this->voyageModel
                ->select('metro_voyage.*, loading.portName as loadingPortName, arrival.portName as arrivalPortName')
                ->join('metro_port as loading', 'loading.id = metro_voyage.loadingPort', 'left')
                ->join('metro_port as arrival', 'arrival.id = metro_voyage.arrivalPort', 'left')
                ->where('vesselId', $vesselId)
                ->orderBy('createdAt', 'DESC')
                ->findAll();

            $html = $this->renderVoyagesRows($voyages);

            return $this->response->setJSON([
                'status' => 1,
                'msg' => $existingVoyageId ? 'Voyage updated successfully' : 'Voyage added successfully',
                'html' => $html,
                'token' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 0, 'msg' => $e->getMessage(), 'token' => csrf_hash()]);
        }
    }

    public function delete($id)
    {
        try {
            $voyage = $this->voyageModel->find($id);
            if ($voyage && $this->voyageModel->delete($id)) {
                return $this->response->setJSON(['status' => 1, 'msg' => 'Voyage deleted', 'token' => csrf_hash()]);
            }
            return $this->response->setJSON(['status' => 0, 'msg' => 'Failed to delete', 'token' => csrf_hash()]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 0, 'msg' => $e->getMessage(), 'token' => csrf_hash()]);
        }
    }

    // Get Vessel Details (Dropdown selection)
    public function getVesselDetails($id)
    {
        try {
            $vessel = $this->vesselsModel->find($id);
            $tanks = $this->tankModel->where('vesselId', $id)->orderBy('tankName', 'ASC')->findAll();

            if (!$vessel) {
                return $this->response->setJSON(['status' => 0, 'msg' => 'Vessel not found']);
            }

            // Render Details HTML using Heredoc
            $detailsHtml = $this->renderVesselDetails($vessel, $tanks);

            return $this->response->setJSON([
                'status' => 1,
                'html' => $detailsHtml,
                'token' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 0, 'msg' => $e->getMessage(), 'token' => csrf_hash()]);
        }
    }

    // Get Voyage for Edit
    public function edit($id)
    {
        try {
            $voyage = $this->voyageModel->find($id);
            if ($voyage) {
                return $this->response->setJSON(['status' => 1, 'data' => $voyage, 'token' => csrf_hash()]);
            }
            return $this->response->setJSON(['status' => 0, 'msg' => 'Voyage not found', 'token' => csrf_hash()]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 0, 'msg' => $e->getMessage(), 'token' => csrf_hash()]);
        }
    }

    // View Voyage Details Page
    public function voyageDetails($voyageId)
    {
       
            $data['page'] = [
                'heading' => 'Voyage Details',
                'title' => 'Voyage Details'
            ];
            // Fetch Voyage with Ports
            $voyage = $this->voyageModel
                ->select('metro_voyage.*, loading.portName as loadingPortName, arrival.portName as arrivalPortName')
                ->join('metro_port as loading', 'loading.id = metro_voyage.loadingPort', 'left')
                ->join('metro_port as arrival', 'arrival.id = metro_voyage.arrivalPort', 'left')
                ->where('metro_voyage.voyageId', $voyageId)
                ->first();

            if (!$voyage) {
                return redirect()->to('metrology/voyages')->with('error', 'Voyage not found');
            }

            // Fetch Vessel
            $vessel = $this->vesselsModel->find($voyage->vesselId);
            $tanks = $this->tankModel->where('vesselId', $voyage->vesselId)->orderBy('tankName', 'ASC')->findAll();

            // Fetch Products
            $db = \Config\Database::connect();
            $products = $db->table('metro_voyageProducts')
                ->select('metro_voyageProducts.*, p.productName')
                ->join('metro_products as p', 'p.id = metro_voyageProducts.productId', 'left')
                ->where('voyageId', $voyageId)
                ->get()->getResult();

            $data['voyage'] = $voyage;
            $data['vessel'] = $vessel;
            $data['tanks'] = $tanks;
            $data['products'] = $products;
            $data['voyageId'] = $voyageId;
            $data['productId'] = '001';

            return view('App\Modules\MetrologicalSupervision\Views\Voyages\VoyageDetails', $data);
        
    }

    // --- Private Render Methods (Heredoc) ---

    private function renderVoyagesRows($voyages)
    {
        $html = '';
        foreach ($voyages as $voyage) {
            // Using Heredoc for row
            $arrival = $voyage->arrivalDate ? date('d M Y H:i', strtotime($voyage->arrivalDate)) : '-';
            $loading = $voyage->loadingPortName ?? '-';
            $viewDetailsUrl = base_url('metrology/voyages-details/' . $voyage->voyageId);

            $html .= <<<HTML
            <tr class="voyage-row" data-id="{$voyage->voyageId}" style="cursor: pointer;" onclick="selectVoyage('{$voyage->voyageId}', '{$voyage->voyageNumber}')">
                <td>{$voyage->voyageNumber}</td>
                <td>{$voyage->arrivalPortName}</td>
                <td>{$arrival}</td>
                <td>
                    <a href="{$viewDetailsUrl}" class="btn btn-sm btn-outline-secondary mr-1" onclick="event.stopPropagation();" title="View Details">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <button class="btn btn-sm btn-outline-info mr-1" onclick="event.stopPropagation(); editVoyage('{$voyage->voyageId}')" title="Edit">
                        <i class="far fa-pen"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-voyage" onclick="event.stopPropagation(); deleteVoyage('{$voyage->voyageId}')" title="Delete">
                        <i class="far fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
HTML;
        }

        if (empty($html)) {
            $html = '<tr><td colspan="4" class="text-center">No voyages found.</td></tr>';
        }
        return $html;
    }

    private function renderVesselDetails($vessel, $tanks)
    {
        // Tanks Grid Item
        $tanksHtml = '';
        if (!empty($tanks)) {
            $tanksHtml = '<div class="row">';
            foreach ($tanks as $tank) {
                // Use col-6 for 2x2 layout
                $tanksHtml .= <<<HTML
                <div class="col-6 mb-2">
                    <div class="p-2 border rounded bg-light">
                        <i class="fas fa-database text-secondary mr-2"></i> {$tank->tankName}
                    </div>
                </div>
HTML;
            }
            $tanksHtml .= '</div>';
        } else {
            $tanksHtml = '<div class="alert alert-light text-center">No tanks recorded.</div>';
        }

        // Complete Card Body HTML
        return <<<HTML
        <div class="row">
            <div class="col-md-5">
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="card-title"><i class="far fa-id-card mr-2"></i> Vessel Profile</h3>
                    </div>
                    <div class="card-body">
                         <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>Vessel Name:</strong> 
                                <span class="">{$vessel->vesselName}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>IMO Number:</strong> 
                                <span class="text-muted">{$vessel->imoNumber}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>Country:</strong> 
                                <span class="text-muted">{$vessel->country}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                 <div class="card h-100">
                    <div class="card-header">
                        <h3 class="card-title"><i class="far fa-database mr-2"></i> Tank Configuration</h3>
                    </div>
                    <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                        {$tanksHtml}
                    </div>
                 </div>
            </div>
        </div>
HTML;
    }
}
