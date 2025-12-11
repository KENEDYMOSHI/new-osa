<?php

namespace App\Libraries;

use App\Models\AppModel;

class StickerLibrary
{
   protected $appModel;
   protected $user;

   public function __constructor()
   {
      $this->appModel = new AppModel();
      $this->user = auth()->user();
   }



   public function generateSticker($quantity, $activity, $controlNumber, $instrumentId, $instrumentName)
   {
      $appModel = new AppModel();
      $user = auth()->user();

      switch ($activity) {
         case setting('Gfs.vtv'):
            $prefix = 'VT';
            $duration = 1;
            break;
         case setting('Gfs.sbl'):
            $prefix = 'SBL';
            $duration = 1;
            break;
         case setting('Gfs.wagonTank'):
            $prefix = 'WGT';
            $duration = 1;
            break;
         case setting('Gfs.fuelPump'):
            $prefix = 'FP';
            $duration = 1;
            break;
         case setting('Gfs.cngFillingStation'):
            $prefix = 'CNG';
            $duration = 1;
            break;
         case setting('Gfs.flowMeter'):
            $prefix = 'FM';
            $duration = 1;
            break;
         case setting('Gfs.taxiMeter'):
            $prefix = 'TXM';
            $duration = 1;
            break;
         case setting('Gfs.counterScale'):
            $prefix = 'CS';
            $duration = 1;
            break;
         case setting('Gfs.platformScale'):
            $prefix = 'PS';
            $duration = 1;
            break;
         case setting('Gfs.springBalance'):
            $prefix = 'SB';
            $duration = 1;
            break;
         case setting('Gfs.weigher'):
            $prefix = 'AXW';
            $duration = 1;
            break;
         case setting('Gfs.automaticWeigher'):
            $prefix = 'AUW';
            $duration = 1;
            break;
         case setting('Gfs.beamScale'):
            $prefix = 'BS';
            $duration = 1;
            break;
         case setting('Gfs.weighBridge'):
            $prefix = 'WB';
            $duration = 1;
            break;
         case setting('Gfs.domesticGasMeter'):
            $prefix = 'DGM';
            $duration = 1;
            break;
         case setting('Gfs.fst'):
            $prefix = 'FST';
            $duration = 5;
            break;
         case setting('Gfs.bst'):
            $prefix = 'BST';
            $duration = 5;
            break;
         case setting('Gfs.tapeMeasure'):
            $prefix = 'TPM';
            $duration = 1;
            break;
         case setting('Gfs.suspendedDigitalWare'):
            $prefix = 'SDW';
            $duration = 1;
            break;
         case setting('Gfs.pressureGauges'):
            $prefix = 'PG';
            $duration = 1;
            break;
         case setting('Gfs.checkPump'):
            $prefix = 'CP';
            $duration = 1;
            break;
         case setting('Gfs.provingTank'):
            $prefix = 'PT';
            $duration = 1;
            break;
         case setting('Gfs.metreRule'):
            $prefix = 'MR';
            $duration = 1;
            break;
         case setting('Gfs.electricityMeter'):
            $prefix = 'ELM';
            $duration = 1;
            break;
         case setting('Gfs.koroboi'):
            $prefix = 'KRB';
            $duration = 1;
            break;
         case setting('Gfs.pishi'):
            $prefix = 'PSH';
            $duration = 1;
            break;
         case setting('Gfs.otherMeasuresOfLength'):
            $prefix = 'OML';
            $duration = 1;
            break;
         case setting('Gfs.otherMeasuringInstrument'):
            $prefix = 'OMI';
            $duration = 1;
            break;

         default:
            return '';
            exit;
            break;
      }


      // Fetch the last sticker data for the given activity
      $lastSticker = $appModel->fetchSticker(['activity' => $activity]);

      if (!$lastSticker) {
         // If no data exists, start with the initial sticker value
         $sticker = $prefix . '0000001';
         // Use $sticker here or perform any other operations outside the loop
      } else {
         // If data exists, extract the numeric part and increment it

         //get the letter prefix of sticker number
         $prefix = preg_replace("/[0-9]/", "", $lastSticker->stickerNumber);
         //get the numeric part
         $numericPart = (int) preg_replace("/[^0-9]/", "", $lastSticker->stickerNumber);
      }

      $stickers = [];
      // generate sticker based on quantity
      for ($i = 0; $i < $quantity; $i++) {
         if (isset($sticker)) {
            // If $sticker is set (meaning it's the initial value), use it as is
            $currentSticker = $sticker;
            unset($sticker); // Unset $sticker so it won't be used in subsequent iterations
         } else {
            // Increment the numeric part and generate the sticker
            $numericPart++;
            // combine prefix and incremented part
            $currentSticker = $prefix . sprintf('%07d', $numericPart);
         }

         $verificationDate = date('Y-m-d H:i:s'); // current date and time
        // $duration = 1; // specify the number of years to add
         $dueDate = date('Y-m-d H:i:s', strtotime("+$duration years", strtotime($verificationDate)));

         $stickerData = [
            'stickerId' => randomString(),
            'activity' => $activity,
            'stickerNumber' => $currentSticker, // Use $currentSticker instead of $sticker
            'verificationDate' => $verificationDate,
            'dueDate' => $dueDate,
            'controlNumber' => $controlNumber,
            'instrumentId' => $instrumentId,
            'instrumentName' => $instrumentName,
            'userId' => $user->unique_id,
            'region' => $user->collection_center,
         ];

         $appModel->addSticker($stickerData);
         $stickers[] = $currentSticker;
      }


      return implode(',', $stickers);
   }


   function attachSticker($items, $controlNumber)
   {

      $itemsArray = array_filter($items, fn($item) => $item['Status'] == 'Pass');

      $codes = [
         setting('Gfs.vtv'),
         setting('Gfs.sbl'),
         setting('Gfs.wagonTank'),
         setting('Gfs.fuelPump'),
         setting('Gfs.cngFillingStation'),
         setting('Gfs.flowMeter'),
         setting('Gfs.taxiMeter'),
         setting('Gfs.counterScale'),
         setting('Gfs.platformScale'),
         setting('Gfs.springBalance'),
         setting('Gfs.weigher'),
         setting('Gfs.automaticWeigher'),
         setting('Gfs.beamScale'),
         setting('Gfs.weighBridge'),
         setting('Gfs.domesticGasMeter'),
         setting('Gfs.fst'),
         setting('Gfs.bst'),
         setting('Gfs.tapeMeasure'),
         setting('Gfs.suspendedDigitalWare'),
         setting('Gfs.pressureGauges'),
         setting('Gfs.checkPump'),
         setting('Gfs.provingTank'),
         setting('Gfs.metreRule'),
         setting('Gfs.electricityMeter'),
         setting('Gfs.koroboi'),
         setting('Gfs.pishi'),
         setting('Gfs.otherMeasuresOfLength'),
         setting('Gfs.otherMeasuringInstrument'),
      ];
      // Iterate through each object
      foreach ($itemsArray as &$object) {
         // Check if the 'gfsCode' is in the specified array
         if (in_array($object['GfsCode'], $codes)) {
            // If yes, get the stickerNumber using $sticker->generateSticker() and add it to the object
            $stickerNumber = $this->generateSticker(
               quantity: $object['ItemQuantity'] ?? 1,
               activity: $object['GfsCode'],
               controlNumber: $controlNumber,
               instrumentId: $object['BillItemRef'],
               instrumentName: $object['ItemName'],

            );
            $object['StickerNumber'] = $stickerNumber;
         } else {
            // If no, set stickerNumber to an empty string
            $object['StickerNumber'] = '';
         }
      }

      return array_map(fn($item) => [
         'StickerNumber' => $item['StickerNumber'],
         'BillItemRef' => $item['BillItemRef'],
      ], $itemsArray);
   }
}
