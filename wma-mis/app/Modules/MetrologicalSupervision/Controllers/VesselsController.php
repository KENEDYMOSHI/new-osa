<?php

namespace App\Modules\MetrologicalSupervision\Controllers;

use App\Controllers\BaseController;
use App\Modules\MetrologicalSupervision\Models\VesselModel;
use App\Modules\MetrologicalSupervision\Models\VesselTankModel;

class VesselsController extends BaseController
{
    protected $vesselModel;
    protected $tankModel;
    protected $view = 'App\Modules\MetrologicalSupervision\Views\Vessels\\';

    public function __construct()
    {
        $this->vesselModel = new VesselModel();
        $this->tankModel = new VesselTankModel();
    }

    public function index()
    {
        $data['page'] = [
            'title'   => 'Vessels',
            'heading' => 'Registered Vessels',
        ];

        helper('country');
        $data['countries'] = getCountries();

        $data['user'] = auth()->user();
        $vessels = $this->vesselModel->findAll();
        $data['vesselsHtml'] = $this->renderVessels($vessels);

        return view($this->view . 'Vessels', $data);
    }

    public function saveVessel()
    {
        $validationRules = [
            'vesselName' => ['rules' => 'required', 'label' => 'Vessel Name'],
            'imoNumber'  => ['rules' => 'required|is_unique[metro_vessels.imoNumber,id,{id}]', 'label' => 'IMO Number'],
            'country'    => ['rules' => 'required', 'label' => 'Country'],
            'id'         => ['rules' => 'permit_empty'],
        ];

        if (!$this->validate($validationRules)) {
            return $this->response->setJSON([
                'status' => 0,
                'errors' => $this->validator->getErrors(),
                'token' => csrf_hash()
            ]);
        }

        $id = $this->request->getPost('id');
        $data = [
            'vesselName' => $this->request->getPost('vesselName'),
            'imoNumber'  => $this->request->getPost('imoNumber'),
            'country'    => $this->request->getPost('country'),
        ];

        if ($id) {
            $this->vesselModel->update($id, $data);
            $message = 'Vessel updated successfully';
        } else {
            $this->vesselModel->insert($data);
            $message = 'Vessel added successfully';
        }

        $vessels = $this->vesselModel->findAll();
        $html = $this->renderVessels($vessels);

        return $this->response->setJSON([
            'status' => 1,
            'msg' => $message,
            'html' => $html,
            'token' => csrf_hash()
        ]);
    }

    public function deleteVessel()
    {
        $id = $this->request->getPost('id');
        if ($this->vesselModel->delete($id)) {
            $vessels = $this->vesselModel->findAll();
            $html = $this->renderVessels($vessels);
            return $this->response->setJSON([
                'status' => 1,
                'msg' => 'Vessel deleted successfully',
                'html' => $html,
                'token' => csrf_hash()
            ]);
        }
        return $this->response->setJSON([
            'status' => 0,
            'msg' => 'Failed to delete vessel',
            'token' => csrf_hash()
        ]);
    }

    public function getTanks()
    {
        $vesselId = $this->request->getPost('vesselId');
        $tanks = $this->tankModel->where('vesselId', $vesselId)->orderBy('tankName', 'ASC')->findAll();
        $html = $this->renderTanks($tanks);
        return $this->response->setJSON([
            'status' => 1,
            'html' => $html,
            'token' => csrf_hash()
        ]);
    }

    public function saveTanks()
    {
        $rules = [
            'vesselId' => 'required',
            'tankNames.*' => [
                'label' => 'Tank Name',
                'rules' => 'required|min_length[2]',
                'errors' => [
                    'required' => 'Tank name is required.',
                    'min_length' => 'Tank name must be at least 2 chars.',
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 0,
                'errors' => $this->validator->getErrors(),
                'token' => csrf_hash()
            ]);
        }

        $vesselId = $this->request->getPost('vesselId');
        $tankNames = $this->request->getPost('tankNames');

        if ($vesselId && !empty($tankNames)) {
            foreach ($tankNames as $name) {
                if (empty(trim($name))) continue;

                $data = [
                    'vesselId' => $vesselId,
                    'tankName' => trim($name),
                    'createdBy' => user_id()
                ];
                $this->tankModel->insert($data);
            }
        }

        $tanks = $this->tankModel->where('vesselId', $vesselId)->orderBy('tankName', 'ASC')->findAll();
        $html = $this->renderTanks($tanks);

        return $this->response->setJSON([
            'status' => 1,
            'msg' => 'Tanks added successfully',
            'html' => $html,
            'token' => csrf_hash()
        ]);
    }

    public function deleteTank()
    {
        $id = $this->request->getPost('id');
        $tank = $this->tankModel->find($id);

        if ($tank && $this->tankModel->delete($id)) {
            $tanks = $this->tankModel->where('vesselId', $tank->vesselId)->orderBy('tankName', 'ASC')->findAll();
            $html = $this->renderTanks($tanks);
            return $this->response->setJSON([
                'status' => 1,
                'msg' => 'Tank deleted successfully',
                'html' => $html,
                'token' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'status' => 0,
            'msg' => 'Failed to delete tank',
            'token' => csrf_hash()
        ]);
    }

    public function updateTank()
    {
        $id = $this->request->getPost('id');
        $tankName = $this->request->getPost('tankName');

        $tank = $this->tankModel->find($id);

        if ($tank) {
            $this->tankModel->update($id, ['tankName' => $tankName]);
            $tanks = $this->tankModel->where('vesselId', $tank->vesselId)->orderBy('tankName', 'ASC')->findAll();
            $html = $this->renderTanks($tanks);

            return $this->response->setJSON([
                'status' => 1,
                'msg' => 'Tank updated successfully',
                'html' => $html,
                'token' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'status' => 0,
            'msg' => 'Failed to update tank',
            'token' => csrf_hash()
        ]);
    }

    private function renderVessels(array $vessels): string
    {
        $html = '';
        $i = 1;
        foreach ($vessels as $row) {
            $createdAt = dateFormatter($row->createdAt); // Assuming helper is available
            $editData = json_encode($row);
            $editArg = "'" . htmlspecialchars($editData, ENT_QUOTES, 'UTF-8') . "'";

            // Add onclick to row for selecting vessel
            $rowClick = "selectVessel('{$row->id}', '{$row->vesselName}')";

            $html .= <<<HTML
                <tr style="cursor: pointer;" onclick="{$rowClick}">
                    <td>{$i}</td>
                    <td>{$row->vesselName}</td>
                    <td>{$row->imoNumber}</td>
                    <td>{$row->country}</td>
                    
                    <td class="text-center">
                        <div class="div">
                            <button type="button" class="btn btn-sm btn-outline-info" title="Edit Vessel" onclick="event.stopPropagation(); editVessel({$editArg})">
                                <i class="far fa-pen"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Vessel" onclick="event.stopPropagation(); deleteVessel('{$row->id}')">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            HTML;
            $i++;
        }
        return $html;
    }

    private function renderTanks(array $tanks): string
    {
        $html = '';
        $i = 1;
        foreach ($tanks as $row) {
            $html .= <<<HTML
                <tr>
                    <td>{$i}</td>
                    <td>{$row->tankName}</td>
                    <td class="text-center">
                        <div class="div">
                            <button type="button" class="btn btn-sm btn-outline-info" title="Edit Tank" onclick="editTank('{$row->id}', '{$row->tankName}')">
                                <i class="far fa-pen"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Tank" onclick="deleteTank('{$row->id}')">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            HTML;
            $i++;
        }
        if (empty($html)) {
            $html = '<tr><td colspan="3" class="text-center">No tanks found</td></tr>';
        }
        return $html;
    }
}
