<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CertModel;
use CodeIgniter\Pager\PagerRenderer;

class CertController extends BaseController
{

    public function index()
    {
        return view('CertView');
    }


    public function getCertificates()
    {
        try {
            $certModel = new CertModel();
            $options = [
                'MONTH(createdAt)' => $this->request->getGet('month'),
                'YEAR(createdAt)' => $this->request->getGet('year'),
                'controlNumber' => $this->request->getGet('controlNumber'),
            ];
            $page = 3;
            $limit = 10; // Number of items per page
            $offset = ($page - 1) * $limit;

            $certData = $certModel->getCertificates($limit, $offset);
            $total =  $certModel->countData();
            $pager = \Config\Services::pager();
            $pages = $pager->makeLinks($page, $limit, $total);
            $links = $pager->links('default', 'customTemplate');


            return $this->response->setJSON([
                'status' => 0,
                'data' => $certData,
                'total' => $total,
                'links' => $links,
            ]);

            exit;




















            $params = array_filter($options, fn($param) => $param  != '');

            $certModel->where($params);



            $perPage = $this->request->getGet('perPage') ?? 16; // Default perPage to 6 if not set
            $page = $this->request->getGet('page') ?? 1; // Default to page 1 if not set

            $certificates = $certModel->paginate($perPage, 'default', $page);
            $pager = $certModel->pager;

            // Set the number of surrounding pages for pagination



            return $this->response->setJSON([
                'certificates'  => $this->renderCertificateData($certificates),
                'params'  => ($params),
                'pager' => $pager,
                'total_pages' => $pager->getPageCount(),
                'currentPage' => $pager->getCurrentPage(),
                'perPage' => $perPage,
                'links' => $pager->links('default', 'customTemplate'),
            ]);
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'trace' => $th->getTrace(),
            ];
        }
        return $this->response->setJSON($response);
    }

    public function renderCertificateData($certificates)
    {
        $tr = '';
        foreach ($certificates as $certificate) {
            $date = dateFormatter($certificate->createdAt);

            // $isPaid = $this->certificateModel->isPaid(['PayCtrNum' => $certificate->controlNumber]);
            $isPaid = true;
            $ol = '';

            $items = json_decode($certificate->items);
            foreach ($items as $item) {
                $ol .= <<<HTML
                     <li>$item</li>
                HTML;
            }

            if ($isPaid) {
                $button = <<<HTML
                    
                        <button data-toggle="tooltip" data-placement="top" title="View Certificate"  type="button" class="btn btn-primary btn-sm" onclick="viewCertificate('$certificate->certificateId')"><i class="fas fa-eye"></i></button>
                           
                HTML;
            } else {
                $button = '<button data-toggle="tooltip" data-placement="top" title="Not Paid"  type="button" class="btn btn-default btn-sm"><i class="fas fa-ban"></i></button>';
            }

            $tr .= <<<HTML
                 <tr>
                    <td>$date</td>
                    <td>$certificate->customer</td>
                    <td>$certificate->certificateNumber</td>
                    <td>$certificate->controlNumber</td>
                    <td style="padding-left:13px">
                       <ol style='padding:0;margin-left: 10px;'>
                        $ol
                       </ol>
                    </td>
                    <td>$button</td>
                </tr>     
            HTML;
        }
        return $tr;
    }
}
