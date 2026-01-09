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

        // Check if license image already exists to avoid regeneration (and expensive API calls)
        $title = str_shuffle('HFEF473THGE7843693475JEGEBJ').$data->licenseNumber . '.jpg';
        $savePath = 'certificates/' . $title;
        $absoluteSavePath = FCPATH . $savePath;
        $imgPath = base_url($savePath);

        // If file exists, return it immediately
        if (file_exists($absoluteSavePath)) {
            return $imgPath;
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

        // Add Instruments List (Dummy Data for now, to be replaced with real user selections later)
        // TODO: Replace with real user selected instruments
        // Calculate available space for centering
        // expiryDateY is the bottom boundary (approx 1416-1495)
        // y is the current position after address (approx 1020)
        
        $listTotalHeight = 40 + (3 * 35); // Header (40) + 3 rows * height (35)
        
        // Add significant padding to skip the "and authorizes him to..." pre-printed text
        $y += 200; 
        
        $availableHeight = $expiryDateY - $y - 30; // Subtract some padding from bottom
        
        // If there is enough space, center it. Otherwise just add padding.
        if ($availableHeight > $listTotalHeight) {
             $paddingTop = ($availableHeight - $listTotalHeight) / 2;
             $y += $paddingTop;
        }
        
        // Move Left: 550 -> 400 -> 200 -> 10
        $startX = 127;

        $instrumentsHeader = "INSTRUMENTS TO BE VERIFIED:";
        $canvas->text(strtoupper($instrumentsHeader), $startX, $y, function ($font) use ($fontBold, $fontSize) {
            $font->size($fontSize); // Same size as other headers
            $font->fileName($fontBold);
            $font->color('#333333');
        });

        $y += 40; // Space for header

        $dummyInstruments = [
            '1. ELECTRONIC BALANCE',
            '2. WEIGHTS (CLASS F1)',
            '3. WEIGHTS (CLASS M1)',
            '4. COUNTER SCALES',
            '5. PLATFORM SCALES',
            '6. WEIGHBRIDGE'
        ];

        $col1X = $startX; // Align with header
        $col2X = $startX + 400; // Second column offset by 400
        $instrLineHeight = 35;

        $half = ceil(count($dummyInstruments) / 2);

        for ($i = 0; $i < $half; $i++) {
             // Left Column
             if (isset($dummyInstruments[$i])) {
                 $canvas->text($dummyInstruments[$i], $col1X, $y + ($i * $instrLineHeight), function($font) use ($fontSemiBold) {
                     $font->size(20); // Slightly smaller font for list
                     $font->fileName($fontSemiBold);
                     $font->color('#333333');
                 });
             }
             
             // Right Column
             if (isset($dummyInstruments[$i + $half])) {
                 $canvas->text($dummyInstruments[$i + $half], $col2X, $y + ($i * $instrLineHeight), function($font) use ($fontSemiBold) {
                     $font->size(20);
                     $font->fileName($fontSemiBold);
                     $font->color('#333333');
                 });
             }
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

        // Save using the paths defined at start
        $canvas->toJpeg()->save($absoluteSavePath);

        return $imgPath;
    }
}