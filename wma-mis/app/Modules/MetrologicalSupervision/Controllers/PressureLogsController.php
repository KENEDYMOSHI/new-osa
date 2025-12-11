<?php

namespace App\Modules\MetrologicalSupervision\Controllers;

use App\Controllers\BaseController;
use App\Modules\MetrologicalSupervision\Models\PressureLogModel;

class PressureLogsController extends BaseController
{
    protected $pressureLogModel;

    public function __construct()
    {
        $this->pressureLogModel = new PressureLogModel();
    }

    public function getList($voyageId)
    {
        try {
            $logs = $this->pressureLogModel
                ->where('voyageId', $voyageId)
                ->orderBy('logDate', 'DESC')
                ->orderBy('logTime', 'DESC')
                ->findAll();

            $html = $this->renderPressureLogRows($logs);

            return $this->response->setJSON(['status' => 1, 'html' => $html, 'token' => csrf_hash()]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 0, 'msg' => $e->getMessage(), 'token' => csrf_hash()]);
        }
    }

    public function get($id)
    {
        try {
            $log = $this->pressureLogModel->find($id);
            if ($log) {
                return $this->response->setJSON(['status' => 1, 'data' => $log, 'token' => csrf_hash()]);
            }
            return $this->response->setJSON(['status' => 0, 'msg' => 'Log not found', 'token' => csrf_hash()]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 0, 'msg' => $e->getMessage(), 'token' => csrf_hash()]);
        }
    }

    public function save()
    {
        try {
            $rules = [
                'voyageId' => 'required',
                'vesselId' => 'required',
                'logDate' => 'required',
                'logTime' => 'required',
                'pressure' => 'required',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => 0,
                    'errors' => $this->validator->getErrors(),
                    'token' => csrf_hash()
                ]);
            }

            $data = [
                'voyageId' => $this->request->getPost('voyageId'),
                'vesselId' => $this->request->getPost('vesselId'),
                'logDate' => $this->request->getPost('logDate'),
                'logTime' => $this->request->getPost('logTime'),
                'pressure' => $this->request->getPost('pressure'),
                'rate' => $this->request->getPost('rate'),
            ];

            $id = $this->request->getPost('id');
            if ($id) {
                $data['id'] = $id;
            }

            if ($this->pressureLogModel->save($data)) {
                $logs = $this->pressureLogModel
                    ->where('voyageId', $data['voyageId'])
                    ->orderBy('logDate', 'DESC')
                    ->orderBy('logTime', 'DESC')
                    ->findAll();

                $html = $this->renderPressureLogRows($logs);

                return $this->response->setJSON(['status' => 1, 'msg' => 'Pressure log saved', 'html' => $html, 'token' => csrf_hash()]);
            } else {
                return $this->response->setJSON(['status' => 0, 'msg' => 'Failed to save', 'token' => csrf_hash()]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 0, 'msg' => $e->getMessage(), 'token' => csrf_hash()]);
        }
    }

    public function delete($id)
    {
        try {
            if ($this->pressureLogModel->delete($id)) {
                return $this->response->setJSON(['status' => 1, 'msg' => 'Log entry deleted', 'token' => csrf_hash()]);
            }
            return $this->response->setJSON(['status' => 0, 'msg' => 'Failed to delete', 'token' => csrf_hash()]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 0, 'msg' => $e->getMessage(), 'token' => csrf_hash()]);
        }
    }


    private function renderPressureLogRows($logs)
    {
        $html = '';
        if (empty($logs)) {
            return '<tr><td colspan="4" class="text-center text-muted py-4"><i class="far fa-tachometer-alt fa-2x mb-2 d-block"></i>No pressure logs found.</td></tr>';
        }
        foreach ($logs as $log) {
            $formattedDate = date('d M Y', strtotime($log->logDate));
            $formattedTime = date('H:i', strtotime($log->logTime));

            $html .= <<<HTML
            <tr>
                <td class="align-middle">
                    <span class="font-weight-bold text-dark">{$formattedDate}</span> <span class="badge badge-light border ml-1">{$formattedTime}</span>
                </td>
                <td class="align-middle">
                    <span class="font-weight-bold">{$log->pressure}</span> <span class="text-muted text-xs">Bar</span>
                </td>
                <td class="align-middle">
                    <span>{$log->rate}</span> <span class="text-muted text-xs">MT/Hr</span>
                </td>
                <td class="text-center align-middle">
                     <button type="button" class="btn btn-sm btn-outline-info" title="Edit" onclick="editPressureLog({$log->id})">
                        <i class="far fa-pen"></i>
                     </button>
                     <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="deletePressureLog({$log->id})">
                        <i class="far fa-trash-alt"></i>
                     </button>
                </td>
            </tr>
HTML;
        }
        return $html;
    }
}
