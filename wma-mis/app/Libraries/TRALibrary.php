<?php
namespace App\Libraries;
use App\TRA\AES;
use App\TRA\RSA;
class TRALibrary
{
    function decryptKey($data, $privateKey)
    {
        $rsa = new RSA();
        $rsa->loadKey($privateKey);
        $rsa->setMGFHash('sha256');
        $rsa->setHash('sha256');
        $rsa->setEncryptionMode(RSA::ENCRYPTION_OAEP);

        return $rsa->decrypt(base64_decode($data));
    }
    function decryptData($base64key, $encryptedData)
    {
        $aes = new AES(AES::MODE_CBC);
        $key = base64_decode($base64key);
        $iv = substr($key, 0, 16);
        $aes->setKey($key);
        $aes->setIV($iv);
        return $aes->decrypt(base64_decode($encryptedData));
    }
    function extractTRAData($encryptedKey, $encryptedData)
    {
        $privateKey = file_get_contents("cert/WMA.pem");
        $key = $this->decryptKey($encryptedKey, $privateKey);
        $output = $this->decryptData(base64_encode($key), $encryptedData);
        return json_decode($output);

    }
}