<?php

namespace App\Modules\MetrologicalSupervision\Controllers;

use App\Controllers\BaseController;
use App\Modules\MetrologicalSupervision\Models\ProductModel;
use App\Modules\MetrologicalSupervision\Models\PortModel;
use App\Modules\MetrologicalSupervision\Models\BerthModel;
use App\Modules\MetrologicalSupervision\Models\TerminalModel;
use App\Modules\MetrologicalSupervision\Models\DocumentModel;

class SettingsController extends BaseController
{
    protected $productModel;
    protected $portModel;
    protected $berthModel;
    protected $terminalModel;
    protected $documentModel;
    protected $view;
    protected $token;

    public function __construct()
    {
        $this->view = "App\Modules\MetrologicalSupervision\Views\\";
        $this->productModel = new ProductModel();
        $this->portModel = new PortModel();
        $this->berthModel = new BerthModel();
        $this->terminalModel = new TerminalModel();
        $this->documentModel = new DocumentModel();
        $this->token = csrf_hash();
        helper(['form', 'date']);
    }

    public function getVariable($var)
    {
        return $this->request->getPost($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public function index()
    {
        $data['page'] = [
            'title'   => 'Settings',
            'heading' => 'Metrological Settings',
        ];

        $data['user'] = auth()->user();

        // Products Data
        $products = $this->productModel->findAll();
        $data['productsHtml'] = $this->renderProducts($products);

        // Ports Data
        $ports = $this->portModel->findAll();
        $data['portsHtml'] = $this->renderPorts($ports);
        $data['ports'] = $ports; // For Select2

        // Berths Data
        $berths = $this->berthModel->select('metro_berths.*, metro_port.portName')->join('metro_port', 'metro_port.id = metro_berths.portId')->findAll();
        $data['berthsHtml'] = $this->renderBerths($berths);

        // Terminals Data
        $terminals = $this->terminalModel->findAll();
        $data['terminalsHtml'] = $this->renderTerminals($terminals);

        // Documents Data
        $documents = $this->documentModel->findAll();
        $data['documentsHtml'] = $this->renderDocuments($documents);

        $data['activeTab'] = 'products';

        return view($this->view . 'Settings\Settings', $data);
    }

    // --- Products CRUD ---

    public function saveProduct()
    {
        $validationRules = ['productName' => ['rules' => 'required', 'label' => 'Product Name']];

        if (!$this->validate($validationRules)) return $this->validationError();

        $id = $this->getVariable('id');
        $data = ['productName' => $this->getVariable('productName')];

        try {
            if (!empty($id)) {
                $this->productModel->update($id, $data);
                $msg = 'Product updated successfully';
            } else {
                $this->productModel->insert($data);
                $msg = 'Product added successfully';
            }
            $products = $this->productModel->findAll();
            return $this->response->setJSON([
                'status' => 1,
                'msg' => $msg,
                'table' => $this->renderProducts($products),
                'token' => $this->token
            ]);
        } catch (\Exception $e) {
            return $this->exceptionError($e);
        }
    }

    public function deleteProduct()
    {
        $id = $this->getVariable('id');
        if ($this->productModel->delete($id)) {
            $products = $this->productModel->findAll();
            return $this->response->setJSON([
                'status' => 1,
                'msg' => 'Product deleted successfully',
                'table' => $this->renderProducts($products),
                'token' => $this->token
            ]);
        }
        return $this->deleteError('Product');
    }

    private function renderProducts(array $products): string
    {
        $html = '';
        $i = 1;
        foreach ($products as $row) {
            $createdAt = dateFormatter($row->createdAt);
            $updatedAt = dateFormatter($row->updatedAt);
            $editArg = "'{$row->id}', '{$row->productName}'";

            $html .= <<<HTML
                <tr>
                    <td>{$i}</td>
                    <td>{$row->productName}</td>
                    <td>{$createdAt}</td>
                    <td>{$updatedAt}</td>
                    <td class="text-center">
                        <div class="div">
                             <button type="button" class="btn btn-sm btn-outline-info" title="Edit" onclick="editProduct({$editArg})">
                                <i class="far fa-pen"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="deleteProduct('{$row->id}')">
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

    // --- Ports CRUD ---

    public function savePort()
    {
        $validationRules = ['portName' => ['rules' => 'required', 'label' => 'Port Name']];

        if (!$this->validate($validationRules)) return $this->validationError();

        $id = $this->getVariable('id');
        $data = ['portName' => $this->getVariable('portName')];

        try {
            if (!empty($id)) {
                $this->portModel->update($id, $data);
                $msg = 'Port updated successfully';
            } else {
                $this->portModel->insert($data);
                $msg = 'Port added successfully';
            }
            $ports = $this->portModel->findAll();
            return $this->response->setJSON([
                'status' => 1,
                'msg' => $msg,
                'table' => $this->renderPorts($ports),
                'portOptions' => $this->renderPortOptions($ports),
                'token' => $this->token
            ]);
        } catch (\Exception $e) {
            return $this->exceptionError($e);
        }
    }

    public function deletePort()
    {
        $id = $this->getVariable('id');
        if ($this->portModel->delete($id)) {
            $ports = $this->portModel->findAll();
            return $this->response->setJSON([
                'status' => 1,
                'msg' => 'Port deleted successfully',
                'table' => $this->renderPorts($ports),
                'portOptions' => $this->renderPortOptions($ports),
                'token' => $this->token
            ]);
        }
        return $this->deleteError('Port');
    }

    private function renderPorts(array $ports): string
    {
        $html = '';
        $i = 1;
        foreach ($ports as $row) {
            $createdAt = dateFormatter($row->createdAt);
            $updatedAt = dateFormatter($row->updatedAt);
            $editArg = "'{$row->id}', '{$row->portName}'";

            $html .= <<<HTML
                <tr>
                    <td>{$i}</td>
                    <td>{$row->portName}</td>
                    <td>{$createdAt}</td>
                    <td>{$updatedAt}</td>
                    <td class="text-center">
                        <div class="div">
                            <button type="button" class="btn btn-sm btn-outline-info" title="Edit" onclick="editPort({$editArg})">
                                <i class="far fa-pen"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="deletePort('{$row->id}')">
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

    private function renderPortOptions(array $ports): string
    {
        $html = '<option value="">Select Port</option>';
        foreach ($ports as $port) {
            $html .= <<<HTML
                <option value="{$port->id}">{$port->portName}</option>
            HTML;
        }
        return $html;
    }

    // --- Berths CRUD ---

    public function saveBerth()
    {
        $validationRules = [
            'berthName' => ['rules' => 'required', 'label' => 'Berth Name'],
            'portId'    => ['rules' => 'required', 'label' => 'Port']
        ];
        if (!$this->validate($validationRules)) return $this->validationError();

        $id = $this->getVariable('id');
        $data = ['berthName' => $this->getVariable('berthName'), 'portId' => $this->getVariable('portId')];

        try {
            if (!empty($id)) {
                $this->berthModel->update($id, $data);
                $msg = 'Berth updated successfully';
            } else {
                $this->berthModel->insert($data);
                $msg = 'Berth added successfully';
            }
            $berths = $this->berthModel->select('metro_berths.*, metro_port.portName')->join('metro_port', 'metro_port.id = metro_berths.portId')->findAll();
            return $this->response->setJSON([
                'status' => 1,
                'msg' => $msg,
                'table' => $this->renderBerths($berths),
                'token' => $this->token
            ]);
        } catch (\Exception $e) {
            return $this->exceptionError($e);
        }
    }

    public function deleteBerth()
    {
        $id = $this->getVariable('id');
        if ($this->berthModel->delete($id)) {
            $berths = $this->berthModel->select('metro_berths.*, metro_port.portName')->join('metro_port', 'metro_port.id = metro_berths.portId')->findAll();
            return $this->response->setJSON([
                'status' => 1,
                'msg' => 'Berth deleted successfully',
                'table' => $this->renderBerths($berths),
                'token' => $this->token
            ]);
        }
        return $this->deleteError('Berth');
    }

    private function renderBerths(array $berths): string
    {
        $html = '';
        $i = 1;
        foreach ($berths as $row) {
            $createdAt = dateFormatter($row->createdAt);
            $updatedAt = dateFormatter($row->updatedAt);
            $editData = json_encode(['id' => $row->id, 'name' => $row->berthName, 'portId' => $row->portId]);
            $editArg = "'" . htmlspecialchars($editData, ENT_QUOTES, 'UTF-8') . "'";

            $html .= <<<HTML
                <tr>
                    <td>{$i}</td>
                    <td>{$row->berthName}</td>
                    <td>{$row->portName}</td>
                    <td>{$createdAt}</td>
                    <td>{$updatedAt}</td>
                    <td class="text-center">
                        <div class="div">
                             <button type="button" class="btn btn-sm btn-outline-info" title="Edit" onclick="editBerth({$editArg})">
                                <i class="far fa-pen"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="deleteBerth('{$row->id}')">
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

    // --- Terminals CRUD ---

    public function saveTerminal()
    {
        $validationRules = [
            'terminalName'    => ['rules' => 'required', 'label' => 'Terminal Name'],
            'postalAddress'   => ['rules' => 'required', 'label' => 'Postal Address'],
            'phoneNumber'     => ['rules' => 'required', 'label' => 'Phone Number'],
            'telephone'       => ['rules' => 'permit_empty', 'label' => 'Telephone'],
            'email'           => ['rules' => 'required|valid_email', 'label' => 'Email'],
            'physicalAddress' => ['rules' => 'required', 'label' => 'Physical Address'],
        ];

        if (!$this->validate($validationRules)) return $this->validationError();

        $id = $this->getVariable('id');
        $data = [
            'terminalName'    => $this->getVariable('terminalName'),
            'postalAddress'   => $this->getVariable('postalAddress'),
            'phoneNumber'     => $this->getVariable('phoneNumber'),
            'telephone'       => $this->getVariable('telephone'),
            'email'           => $this->getVariable('email'),
            'physicalAddress' => $this->getVariable('physicalAddress'),
        ];

        try {
            if (!empty($id)) {
                $this->terminalModel->update($id, $data);
                $msg = 'Terminal updated successfully';
            } else {
                $this->terminalModel->insert($data);
                $msg = 'Terminal added successfully';
            }
            $terminals = $this->terminalModel->findAll();
            return $this->response->setJSON([
                'status' => 1,
                'msg' => $msg,
                'table' => $this->renderTerminals($terminals),
                'token' => $this->token
            ]);
        } catch (\Exception $e) {
            return $this->exceptionError($e);
        }
    }

    public function deleteTerminal()
    {
        $id = $this->getVariable('id');
        if ($this->terminalModel->delete($id)) {
            $terminals = $this->terminalModel->findAll();
            return $this->response->setJSON([
                'status' => 1,
                'msg' => 'Terminal deleted successfully',
                'table' => $this->renderTerminals($terminals),
                'token' => $this->token
            ]);
        }
        return $this->deleteError('Terminal');
    }

    private function renderTerminals(array $terminals): string
    {
        $html = '';
        $i = 1;
        foreach ($terminals as $row) {
            $createdAt = dateFormatter($row->createdAt);
            $updatedAt = dateFormatter($row->updatedAt);
            $editData = json_encode([
                'id' => $row->id,
                'name' => $row->terminalName,
                'postal' => $row->postalAddress,
                'phone' => $row->phoneNumber,
                'telephone' => $row->telephone,
                'email' => $row->email,
                'physical' => $row->physicalAddress
            ]);
            $editArg = "'" . htmlspecialchars($editData, ENT_QUOTES, 'UTF-8') . "'";

            $html .= <<<HTML
                <tr>
                    <td>{$i}</td>
                    <td>{$row->terminalName}</td>
                    <td>{$row->phoneNumber}</td>
                    <td>{$row->telephone}</td>
                    <td>{$row->email}</td>
                    <td>{$createdAt}</td>
                    <td>{$updatedAt}</td>
                    <td class="text-center">
                         <div class="div">
                             <button type="button" class="btn btn-sm btn-outline-info" title="Edit" onclick="editTerminal({$editArg})">
                                <i class="far fa-pen"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="deleteTerminal('{$row->id}')">
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

    // --- Documents CRUD ---

    public function saveDocument()
    {
        $validationRules = ['documentName' => ['rules' => 'required', 'label' => 'Document Name']];
        if (!$this->validate($validationRules)) return $this->validationError();

        $id = $this->getVariable('id');
        $data = ['documentName' => $this->getVariable('documentName')];

        try {
            if (!empty($id)) {
                $this->documentModel->update($id, $data);
                $msg = 'Document updated successfully';
            } else {
                $this->documentModel->insert($data);
                $msg = 'Document added successfully';
            }
            $documents = $this->documentModel->findAll();
            return $this->response->setJSON([
                'status' => 1,
                'msg' => $msg,
                'table' => $this->renderDocuments($documents),
                'token' => $this->token
            ]);
        } catch (\Exception $e) {
            return $this->exceptionError($e);
        }
    }

    public function deleteDocument()
    {
        $id = $this->getVariable('id');
        if ($this->documentModel->delete($id)) {
            $documents = $this->documentModel->findAll();
            return $this->response->setJSON([
                'status' => 1,
                'msg' => 'Document deleted successfully',
                'table' => $this->renderDocuments($documents),
                'token' => $this->token
            ]);
        }
        return $this->deleteError('Document');
    }

    private function renderDocuments(array $documents): string
    {
        $html = '';
        $i = 1;
        foreach ($documents as $row) {
            $createdAt = dateFormatter($row->createdAt);
            $updatedAt = dateFormatter($row->updatedAt);
            $editArg = "'{$row->id}', '{$row->documentName}'";

            $html .= <<<HTML
                <tr>
                    <td>{$i}</td>
                    <td>{$row->documentName}</td>
                    <td>{$createdAt}</td>
                    <td>{$updatedAt}</td>
                    <td class="text-center">
                        <div class="div">
                             <button type="button" class="btn btn-sm btn-outline-info" title="Edit" onclick="editDocument({$editArg})">
                                <i class="far fa-pen"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="deleteDocument('{$row->id}')">
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

    // --- Common Error Helpers ---

    private function validationError()
    {
        return $this->response->setJSON(['status' => 0, 'errors' => $this->validator->getErrors(), 'msg' => 'Validation failed', 'token' => $this->token]);
    }

    private function exceptionError($e)
    {
        return $this->response->setJSON(['status' => 0, 'msg' => 'Something went wrong: ' . $e->getMessage(), 'token' => $this->token]);
    }

    private function deleteError($entityName)
    {
        return $this->response->setJSON(['status' => 0, 'msg' => "Failed to delete $entityName", 'token' => $this->token]);
    }
}
