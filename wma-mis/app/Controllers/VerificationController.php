<?php

namespace App\Controllers;

use App\Models\AppModel;
use App\Models\VtcModel;
use App\Models\UsersModel;
use App\Models\WmaBillModel;
use App\Models\CustomerModel;
use App\Models\CertificateModel;
use CodeIgniter\Shield\Models\UserModel;

class VerificationController extends BaseController
{

    protected $appModel;
    protected $certificateModel;
    protected $customerModel;
    protected $vtvModel;
    protected $usersModel;



    public function __construct()
    {
        $this->appModel = new AppModel();
        $this->customerModel = new CustomerModel();
        $this->certificateModel = new CertificateModel();
        $this->vtvModel = new VtcModel();
        $this->usersModel = new UsersModel();
    }


    public function verifySticker($stickerId)
    {
        // Default response data
        $data = [
            'sticker' => null,
            'error' => false,  // Add error key, default false
        ];

        $userModel = new UserModel();

        if (empty($stickerId)) {
            $data['error'] = true; // No sticker id provided
            return view('Pages/Search/StickerVerification', $data);
        }

        try {
            $sticker = $this->appModel->fetchSticker(['stickerId' => $stickerId]);
            if (!$sticker) {
                $data['error'] = true;
                return view('Pages/Search/StickerVerification', $data);
            }

            $certificate = $this->certificateModel->fetchCorrectnessCertificate(['controlNumber' => $sticker->controlNumber]);
            if (!$certificate) {
                $data['error'] = true;
                return view('Pages/Search/StickerVerification', $data);
            }

            $officerData = $userModel->select('username')->where('unique_id', $sticker->userId)->first();
            if (!$officerData) {
                $data['error'] = true;
                return view('Pages/Search/StickerVerification', $data);
            }
            $officer = $officerData->username;

            $billModel = new WmaBillModel();
            $bill = $billModel->getBill($sticker->controlNumber);
            if (!$bill) {
                $data['error'] = true;
                return view('Pages/Search/StickerVerification', $data);
            }

            // Prepare sticker verification data
            $data['sticker'] = (object)[
                'customer' => ucwords(strtolower($certificate->customer)),
                'mobile' => $certificate->mobile,
                'region' => str_replace('Wakala Wa Vipimo', '', wmaCenter($sticker->region)->centerName),
                'activity' => activityName($sticker->activity),
                'instrument' => $sticker->instrumentName,
                'stickerNumber' => $sticker->stickerNumber,
                'verificationDate' => dateFormatter($sticker->verificationDate),
                'nextVerification' => dateFormatter($sticker->dueDate),
                'certificateNumber' => $certificate->certificateNumber,
                'controlNumber' => $sticker->controlNumber,
                'amount' => number_format($bill->BillAmt),
                'paymentStatus' => $bill->PaymentStatus,
                'verifiedBy' => $officer,
            ];
        } catch (\Throwable $e) {
            log_message('error', 'Sticker verification failed: ' . $e->getMessage());
            $data['error'] = true;
        }

        return view('Pages/Search/StickerVerification', $data);
    }


    public function verifyCalibrationChart($customerId, $vehicleId, $region)
    {
        $id = strstr($vehicleId, '-', true);
        $vehicle = $this->vtvModel->findVehicleTank(['vehicle_tanks.id' => $id]);

        $verification = $this->vtvModel->verifiedVtv(['original_id' => $vehicle->data_id]);
        $data['isVerified'] = (bool) $verification;

        $data['officer'] = $this->usersModel->select('username')->where('unique_id', $vehicle->unique_id)->first()->username;
        $data['vehicle'] = $vehicle;
        $data['verification'] = $verification[0] ?? null;



       return view('Pages/Vtc/VerifyVtv', $data);
      

        // printer($data['vehicle']);
        // printer($vehicle);
        // printer($verification);
        // // printer($isVerified);
        // exit;
    }
}
