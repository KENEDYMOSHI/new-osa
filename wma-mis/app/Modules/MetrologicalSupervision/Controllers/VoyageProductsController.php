<?php

namespace App\Modules\MetrologicalSupervision\Controllers;

use App\Controllers\BaseController;
use App\Modules\MetrologicalSupervision\Models\VoyageProductModel;

class VoyageProductsController extends BaseController
{
    protected $productModel;

    public function __construct()
    {
        $this->productModel = new VoyageProductModel();
    }

    public function getProducts($voyageId)
    {
        try {
            $products = $this->productModel
                ->select('metro_voyageProducts.*, p.productName')
                ->join('metro_products as p', 'p.id = metro_voyageProducts.productId', 'left')
                ->where('voyageId', $voyageId)
                ->findAll();

            $html = $this->renderProductsRows($products);

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
                'productId' => 'required',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => 0,
                    'errors' => $this->validator->getErrors(),
                    'token' => csrf_hash()
                ]);
            }

            // Auto Generate ID: PRODUCT + YmdHis + rand
            $voyageId = $this->request->getPost('voyageId');
            $productId = $this->request->getPost('productId');
            $existingId = $this->request->getPost('voyageProductId'); // Hidden field

            $data = [
                'voyageId' => $voyageId,
                'productId' => $productId,
                'loadPortDensityAtFifteen' => $this->request->getPost('loadPortDensityAtFifteen'),
                'loadPortWCFTAtFifteen' => $this->request->getPost('loadPortWCFTAtFifteen'),
                'loadPortDensityAtTwenty' => $this->request->getPost('loadPortDensityAtTwenty'),
                'loadPortWCFTAtTwenty' => $this->request->getPost('loadPortWCFTAtTwenty'),
                'tbsDensityAtFifteen' => $this->request->getPost('tbsDensityAtFifteen'),
                'tbsWCFTAtFifteen' => $this->request->getPost('tbsWCFTAtFifteen'),
                'tbsDensityAtTwenty' => $this->request->getPost('tbsDensityAtTwenty'),
                'tbsWCFTAtTwenty' => $this->request->getPost('tbsWCFTAtTwenty'),
                'primaryLine' => $this->request->getPost('primaryLine'),
                'secondaryLine' => $this->request->getPost('secondaryLine'),
                'billOfLading' => $this->request->getPost('billOfLading'),
            ];

            $success = false;

            if ($existingId) {
                // UPDATE
                $success = $this->productModel->update($existingId, $data);
            } else {
                // INSERT
                $id = 'PRODUCT' . date('YmdHis') . rand(1000, 9999);
                $data['voyageProductId'] = $id;
                $success = $this->productModel->insert($data);
            }

            if ($success) {
                // Fetch updated list
                $products = $this->productModel
                    ->select('metro_voyageProducts.*, p.productName')
                    ->join('metro_products as p', 'p.id = metro_voyageProducts.productId', 'left')
                    ->where('voyageId', $voyageId)
                    ->findAll();

                $html = $this->renderProductsRows($products);

                return $this->response->setJSON([
                    'status' => 1,
                    'msg' => $existingId ? 'Product updated' : 'Product saved',
                    'html' => $html,
                    'token' => csrf_hash()
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 0,
                    'msg' => 'Failed to save product',
                    'token' => csrf_hash()
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 0, 'msg' => $e->getMessage(), 'token' => csrf_hash()]);
        }
    }

    public function delete($id)
    {
        try {
            if ($this->productModel->delete($id)) {
                return $this->response->setJSON(['status' => 1, 'msg' => 'Product deleted', 'token' => csrf_hash()]);
            }
            return $this->response->setJSON(['status' => 0, 'msg' => 'Failed to delete', 'token' => csrf_hash()]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 0, 'msg' => $e->getMessage(), 'token' => csrf_hash()]);
        }
    }

    public function getList()
    {
        try {
            $db = \Config\Database::connect();
            $products = $db->table('metro_products')->orderBy('productName', 'ASC')->get()->getResultArray();
            return $this->response->setJSON(['products' => $products, 'token' => csrf_hash()]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 0, 'msg' => $e->getMessage(), 'token' => csrf_hash()]);
        }
    }

    public function get($id)
    {
        try {
            $product = $this->productModel
                ->select('metro_voyageProducts.*, p.productName')
                ->join('metro_products as p', 'p.id = metro_voyageProducts.productId', 'left')
                ->where('voyageProductId', $id)
                ->first();

            if ($product) {
                return $this->response->setJSON(['status' => 1, 'data' => $product, 'token' => csrf_hash()]);
            }
            return $this->response->setJSON(['status' => 0, 'msg' => 'Product not found', 'token' => csrf_hash()]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 0, 'msg' => $e->getMessage(), 'token' => csrf_hash()]);
        }
    }

    // --- Private Render Methods (Heredoc) ---
    private function renderProductsRows($products)
    {
        $html = '';
        foreach ($products as $prod) {
            $html .= <<<HTML
            <tr>
                <td>{$prod->productName}</td>
                <td>{$prod->billOfLading}</td>
                <td>{$prod->loadPortDensityAtFifteen}</td>
                <td>
                    <button class="btn btn-sm btn-outline-info mr-1" onclick="viewProduct('{$prod->voyageProductId}')" title="View Details">
                        <i class="far fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success mr-1" onclick="editProduct('{$prod->voyageProductId}')" title="Edit">
                        <i class="far fa-pen"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct('{$prod->voyageProductId}')" title="Delete">
                        <i class="far fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
HTML;
        }

        if (empty($html)) {
            $html = '<tr><td colspan="4" class="text-center">No products found for this voyage.</td></tr>';
        }
        return $html;
    }
}
