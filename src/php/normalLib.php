<?php
    function AESEncrypt($str, $key = '')
    {
        if (!$key) {
            return false;
        }
        return base64_encode(openssl_encrypt($str, "AES-256-CBC", $key, true, str_repeat(chr(0), 16)));
    }

    function AESDecrypt($str, $key = '')
    {
        if (!$key) {
            return false;
        }
        return openssl_decrypt(base64_decode($str), "AES-256-CBC", $key, true, str_repeat(chr(0), 16));
    }
    
    function PreventXSS($string) {
        $result = stripcslashes($string);
        $result = htmlspecialchars($result);
        return $result;
    }
?>