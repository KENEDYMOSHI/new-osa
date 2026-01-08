<?php namespace App\Libraries;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
class LicenseGenerator
{
    public function generateLicense($data = null)
    {
        

        if ($data === null) {
            return false;
        }



    
     


        $imageManager = new ImageManager(new Driver());

        $ceoSignature = FCPATH . 'LicenseTemplates/ceo-sign.png';

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




        $fontBold = FCPATH . 'assets/fonts/Roboto-Regular.ttf';
        $fontSemiBold = FCPATH . 'assets/fonts/Roboto-Regular.ttf';

        $fontSize = 24;

        // Load the background image
        $background = FCPATH . "LicenseTemplates/$template";
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
        $expiryDate = date('d M Y', strtotime($data->expiryDate));
        $canvas->text(spaces($expiryDate), $expiryDateX, $expiryDateY, function ($font) use ($fontBold, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontBold);
            $font->color('#333333');
        });

        // date of issuing
        $issuingDate = date('d M Y', strtotime($data->createdAt));
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


        // $qrCode = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($qrCodeData);
        // Use external API to generate QR Code
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=230x230&data=' . urlencode($qrCodeData);
        
        try {
            // Fetch image from URL
            $qrContent = file_get_contents($qrUrl);
            if ($qrContent !== false) {
                 $qrCodeImage = $imageManager->read($qrContent);
                 // Resize is handled by API size parameter, but ensure it fits if needed
                 $qrCodeImage->resize(230, 230);
            } else {
                 throw new \Exception("Failed to fetch QR code");
            }
        } catch (\Exception $e) {
            // Fallback to placeholder if offline or API fails
            log_message('error', 'QR Code Generation Failed: ' . $e->getMessage());
            $qrCodeImage = $imageManager->create(230, 230, '#000000');
        }

        // Insert the QR code at the bottom right corner
        // Adjusted X/Y for bottom right alignment
        $canvas->place($qrCodeImage, 'right', 129, 650);

        $title = $data->licenseNumber . '.jpg';
        $savePath = 'certificates/' . $title;
        // $savePath = WRITEPATH . $title;
        // $savePath = 'stickers/' . $title;
        
        // Ensure absolute path for saving
        $absoluteSavePath = FCPATH . $savePath;
        
        $canvas->toJpeg()->save($absoluteSavePath);

        $imgPath = base_url($savePath);

        return $imgPath;
        // echo <<<HTML
        //     <img src="$imgPath" width='700'>         
        // HTML;
    }
}