<?php

// use Endroid\QrCode\Builder\Builder;
// use Endroid\QrCode\Writer\PngWriter;
// use Endroid\QrCode\Encoding\Encoding;
// use Endroid\QrCode\RoundBlockSizeMode;
// use Endroid\QrCode\ErrorCorrectionLevel;


//  function QRCode(array $data)
// {


//     $size = 200;
//     $result = Builder::create()
//         ->writer(new PngWriter())
//         ->writerOptions([])
//         ->data(json_encode($data))
//         ->encoding(new Encoding('UTF-8'))
//         ->errorCorrectionLevel(ErrorCorrectionLevel::High)
//         ->size($size)
//         ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
//         // ->logoPath('assets/images/wma1.png')
//         // ->logoResizeToWidth($size / 2)
//         ->validateResult(false)
//         ->build();


//     header('Content-Type: ' . $result->getMimeType());

//     return $result->getDataUri();

// }

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

function QRCode($data)
{
    $qrData = (is_array($data) || $data instanceof stdClass) ? json_encode($data) : $data;
    $result = Builder::create()
        ->writer(new PngWriter())
        ->writerOptions([])
        ->data($qrData)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(ErrorCorrectionLevel::High)
        ->size(350)
        ->margin(30)
        ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
        ->logoPath('assets/images/wma1.png')
        ->logoResizeToWidth(60)
        ->logoPunchoutBackground(true)
       // ->labelText('This is the label')
        // ->labelFont(new NotoSans(20))
        // ->labelAlignment(LabelAlignment::Center)
        ->validateResult(false)
        ->build();

    header('Content-Type: ' . $result->getMimeType());

    return $result->getDataUri();
}
