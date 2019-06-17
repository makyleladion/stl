<?php
namespace app\System\Utils;

use Illuminate\Encryption\Encrypter;

class EncryptionUtils
{
    private $key;
    private $cipher = "AES-128-CBC";

    public function __construct() {
        $plaintext = env('APP_DESCRIPTION');
        $this->key = substr($plaintext,0,16);
    }
    
    public function encryptSpecial($plaintext)
    {   
        $encrypt = new Encrypter($this->key, $this->cipher);
        return $encrypt->encrypt($plaintext);
    }
    
    public function decryptSpecial($ciphertext)
    {
        $decrypt = new Encrypter($this->key, $this->cipher);
        return $decrypt->decrypt($ciphertext);
    }
}
