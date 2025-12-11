<?php namespace App\Libraries ;


use App\Models\CertificateModel;

class CertificateLibrary{
    protected $user;
    protected $collectionCenter;
    protected $appModel;
    protected $certificateModel;
    protected $token;
    protected $uniqueId;


    public function __construct()
    {
        $this->certificateModel = new CertificateModel();
        helper('date');
        $this->token = csrf_hash();
        $this->user = auth()->user();
        $this->collectionCenter = $this->user->collection_center;
        $this->uniqueId = $this->user->unique_id;



        //     $dumpSettings = array(),
        // $pdoSettings = array()
    }
  // ===================================================================
  public function generateCertificateNumber()
  {



      //get region name
      $region = wmaCenter($this->collectionCenter)->centerName;


      //get 3 first letter of region name
      $prefix = strtoupper(substr($region, 0, 3));


      // Fetch the last sticker data for the given activity
      $lastCertificate = (new CertificateModel())->getLastCorrectnessCertificate(['region' => $this->collectionCenter]);

      if (!$lastCertificate) {
          // If no data exists, start with the initial sticker value
          $certificate = $prefix . '000001';
          // Use $certificate here or perform any other operations outside the loop
      } else {
          // If data exists, extract the numeric part and increment it

          //get the letter prefix of sticker number
          $prefix = preg_replace("/[0-9]/", "", $lastCertificate->certificateNumber);
          //get the numeric part
          $numericPart = (int) preg_replace("/[^0-9]/", "", $lastCertificate->certificateNumber);
      }



      if (isset($certificate)) {
          // If $certificate is set (meaning it's the initial value), use it as is
          $currentCertificate = $certificate;
          //  unset($certificate); // Unset $certificate so it won't be used in subsequent iterations
      } else {
          // Increment the numeric part and generate the sticker
          $numericPart++;
          // combine prefix and incremented part
          $currentCertificate = $prefix . sprintf('%06d', $numericPart);
      }

      return $currentCertificate;

      //  echo $prefix;




  }
  // ===================================================================


  public function createCertificateData($data)
  {

      $certificate = new CertificateModel();
      $params = [
          'certificateId' => randomString(),
          'certificateNumber' => $this->generateCertificateNumber(),
          'activities' => $data->activity,
          'region' => $this->collectionCenter,
          'officer' => $this->uniqueId,
          'customer' => $data->customer,
          'mobile' => $data->mobile,
          'address' => $data->address,
          'controlNumber' => $data->controlNumber,
          'items' => $data->items

      ];

      $certificate->addCorrectnessCertificate($params);
  }
}
