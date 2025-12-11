<?php

namespace App\Modules\MetrologicalSupervision\Controllers;

use App\Controllers\BaseController;
use App\Modules\MetrologicalSupervision\Models\TimeLogModel;

class TimeLogsController extends BaseController
{
    protected $timeLogModel;

    public function __construct()
    {
        $this->timeLogModel = new TimeLogModel();
    }

    public function getList($voyageId)
    {
        try {
            $logs = $this->timeLogModel
                ->where('voyageId', $voyageId)
                ->orderBy('logDate', 'DESC')
                ->orderBy('logTime', 'DESC')
                ->findAll();

            $html = $this->renderTimeLogRows($logs);

            return $this->response->setJSON(['status' => 1, 'html' => $html, 'token' => csrf_hash()]);
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
                'eventDescription' => 'required',
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
                'eventDescription' => $this->request->getPost('eventDescription'),
            ];

            $id = $this->request->getPost('id');
            if ($id) {
                $data['id'] = $id;
            }

            if ($this->timeLogModel->save($data)) {
                $logs = $this->timeLogModel
                    ->where('voyageId', $data['voyageId'])
                    ->orderBy('logDate', 'DESC')
                    ->orderBy('logTime', 'DESC')
                    ->findAll();

                $html = $this->renderTimeLogRows($logs);

                return $this->response->setJSON(['status' => 1, 'msg' => 'Time log saved', 'html' => $html, 'token' => csrf_hash()]);
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
            if ($this->timeLogModel->delete($id)) {
                return $this->response->setJSON(['status' => 1, 'msg' => 'Log entry deleted', 'token' => csrf_hash()]);
            }
            return $this->response->setJSON(['status' => 0, 'msg' => 'Failed to delete', 'token' => csrf_hash()]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 0, 'msg' => $e->getMessage(), 'token' => csrf_hash()]);
        }
    }

    public function get($id)
    {
        try {
            $log = $this->timeLogModel->find($id);
            if ($log) {
                return $this->response->setJSON(['status' => 1, 'data' => $log, 'token' => csrf_hash()]);
            }
            return $this->response->setJSON(['status' => 0, 'msg' => 'Log not found', 'token' => csrf_hash()]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 0, 'msg' => $e->getMessage(), 'token' => csrf_hash()]);
        }
    }

    private function renderTimeLogRows($logs)
    {
        $html = '';
        if (empty($logs)) {
            return '<tr><td colspan="4" class="text-center text-muted py-4"><i class="far fa-clock fa-2x mb-2 d-block"></i>No time logs entries found.</td></tr>';
        }
        foreach ($logs as $log) {
            $formattedDate = date('d M Y', strtotime($log->logDate));
            $formattedTime = date('H:i', strtotime($log->logTime));

            $html .= <<<HTML
            <tr>
                <td class="align-middle">
                    <span class="font-weight-bold text-dark">{$formattedDate}</span>
                </td>
                <td class="align-middle">
                    <span class="badge badge-light border">{$formattedTime}</span>
                </td>
                <td class="align-middle">
                    <span class="text-secondary">{$log->eventDescription}</span>
                </td>
                <td class="text-center align-middle">
                     <button type="button" class="btn btn-sm btn-outline-info" title="Edit" onclick="editTimeLog({$log->id})">
                        <i class="far fa-pen"></i>
                     </button>
                     <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="deleteTimeLog({$log->id})">
                        <i class="far fa-trash-alt"></i>
                     </button>
                </td>
            </tr>
HTML;
        }
        return $html;
    }
}
