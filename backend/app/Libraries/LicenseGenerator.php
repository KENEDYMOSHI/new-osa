<?php namespace App\Libraries;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
class licenseGeneratorLibrary
{
    public function generateLicense($data = null)
    {
        

         // sample data
         $data = (object)[
          'licenseType' => 'Class A',
          'licenseNumber' => '1234567890',
          'createdAt' => '12 Jan 2024',
          'expiryDate' => '12 Jan 2025',
          'applicantName' => 'John Doe',

          
         ];



    
     


        $imageManager = new ImageManager(new Driver());

        $ceoSignature = 'assets/img/ceoSign.png';

        function spaces($string)
        {
            return implode(' ', str_split($string));
        }


        switch ($data->licenseType) {
            case 'Class A':
                $template = 'classA.png';
                $expiryDateX = 590;
                $expiryDateY = 1416;


                $issueDateX = 374;
                $issueDateY = 1468;


                $signatureX = 315;
                $signatureY = 600;

                $xPosition = 600;

                break;
            case 'Class B':
                $template = 'classB.png';
                $expiryDateX = 584;
                $expiryDateY = 1495;


                $issueDateX = 374;
                $issueDateY = 1548;


                $signatureX = 288;
                $signatureY = 630;

                $xPosition = 600;

                break;
            case 'Class C':

                $template = 'classC.png';
                $expiryDateX = 584;
                $expiryDateY = 1495;


                $issueDateX = 374;
                $issueDateY = 1548;


                $signatureX = 315;
                $signatureY = 630;

                $xPosition = 600;

                break;
            case 'Class D':
                $template = 'classD.png';
                $expiryDateX = 584;
                $expiryDateY = 1495;


                $issueDateX = 374;
                $issueDateY = 1548;


                $signatureX = 315;
                $signatureY = 630;

                $xPosition = 600;

                break;
            case 'Class E':
                $template = 'classE.png';
                $expiryDateX = 584;
                $expiryDateY = 1495;


                $issueDateX = 374;
                $issueDateY = 1548;


                $signatureX = 315;
                $signatureY = 630;

                $xPosition = 600;

                break;
            case 'Tank Calibration':
            case 'Tank Construction':

                $template = 'storageTank.png';
                $expiryDateX = 584;
                $expiryDateY = 1427;


                $issueDateX = 374;
                $issueDateY = 1479;


                $signatureX = 315;
                $signatureY = 630;

                $xPosition = 465;

                break;
            case 'Gas Meter Calibration':
                $template = 'gasMeter.png';
                $expiryDateX = 544;
                $expiryDateY = 1422;


                $issueDateX = 333;
                $issueDateY = 1475;


                $signatureX = 315;
                $signatureY = 630;

                $xPosition = 454;
                break;
            case 'Pressure Gauges &amp; Valves Calibration':
            case 'Pressure Gauges & Valves Calibration':
                $template = 'pressureGaugeAndValve.png';
                $expiryDateX = 565;
                $expiryDateY = 1422;


                $issueDateX = 353;
                $issueDateY = 1475;


                $signatureX = 315;
                $signatureY = 630;

                $xPosition = 315;
                break;

            default:
                $template = 'classD.png';
                $expiryDateX = 584;
                $expiryDateY = 1495;


                $issueDateX = 374;
                $issueDateY = 1548;


                $signatureX = 315;
                $signatureY = 630;

                $xPosition = 600;

                break;
        }




        $fontBold = 'assets/fonts/Poppins-Bold.ttf';
        $fontSemiBold = 'assets/fonts/Poppins-SemiBold.ttf';

        $fontSize = 24;

        // Load the background image
        $background = "assets/img/$template";
        $background = $imageManager->read($background);
        // Create a new image instance
        $canvas = $imageManager->create(1414, 2000, '#ffffff'); // Width, Height, Background Color
        //    $img = $imageManager->create(1000, 600, '#ffffff'); // Width, Height, Background Color

        // Insert the background image
        $canvas->place($background, 'top-left');



        //license number
        $licenseNumber = $data->licenseNumber;
        $canvas->text(spaces($licenseNumber), 550, 457, function ($font) use ($fontBold, $fontSize) {
            $font->size(26);
            $font->fileName($fontBold);
            $font->color('#333333');
        });



        //license Type
        // $licenseType = 'PRESSURE GAUGES and valve CALIBRATION';
        $licenseType = html_entity_decode($data->licenseType);

        // if (strlen($licenseType) <= 7) {
        //     $xPosition = 600;
        // } elseif (strlen($licenseType) >= 14 && strlen($licenseType) <= 20) {
        //     $xPosition = 550;
        // } elseif (strlen($licenseType) >= 20) {
        //     $xPosition = 454;
        // }
        $canvas->text(spaces(strtoupper($licenseType)), $xPosition, 600, function ($font) use ($fontBold, $fontSize) {
            $font->size(26);
            $font->fileName($fontBold);
            $font->color('#333333');
        });


        // customer details

        $name = html_entity_decode($data->applicantName);
        $company = html_entity_decode($data->company) != '' ? '|OF ' . html_entity_decode($data->company) . ',' : '';
        $address = $data->address;

        $details = "$name,$company|$address";
        $lines = explode("|", spaces(strtoupper($details)));
        $y = 915;  // Starting Y position
        $lineHeight = 35;  // Adjust line height as needed

        foreach ($lines as $line) {
            $canvas->text($line, 550, $y, function ($font) use ($fontSemiBold, $fontSize) {
                $font->size($fontSize);
                $font->fileName($fontSemiBold);
                $font->color('#333333');
            });
            $y += $lineHeight;  // Move Y down for the next line
        }



        //expiry date
        $expiryDate = dateFormatter($data->expiryDate);
        $canvas->text(spaces($expiryDate), $expiryDateX, $expiryDateY, function ($font) use ($fontBold, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontBold);
            $font->color('#333333');
        });

        // date of issuing
        $issuingDate = dateFormatter($data->createdAt);
        $canvas->text(spaces($issuingDate), $issueDateX, $issueDateY, function ($font) use ($fontBold, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontBold);
            $font->color('#333333');
        });

        if (file_exists($ceoSignature)) {
            //officer signature
            $officerSign = $imageManager->read($ceoSignature);
            $officerSign->resize(250, 60);
            $canvas->place($officerSign, 'left', $signatureX, $signatureY);
        }



        $qrCodeData = base_url('verification/verifyLicense/' . $data->licenseToken);


        $qrCode =  QRCode($qrCodeData);

        // // Load the QR code image and resize it
        $qrCodeImage = $imageManager->read($qrCode);
        $qrCodeImage->resize(230, 230);

        // // Insert the QR code at the bottom right corner
        $canvas->place($qrCodeImage, 'right', 129, 650);

        $title = $data->licenseNumber . '.jpg';
        $savePath = 'certificates/' . $title;
        // $savePath = WRITEPATH . $title;
        // $savePath = 'stickers/' . $title;
        $canvas->toJpeg()->save($savePath);

        $imgPath = base_url($savePath);

        return $imgPath;
        // echo <<<HTML
        //     <img src="$imgPath" width='700'>         
        // HTML;
    }
}