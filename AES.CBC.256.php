<?php
/*
 * Written by Hect0r Xorius <staticpi.net@gmail.com>
 */

// AES CBC 256 Encrypt Plain text with key string.
// Returns, Array with ciphered text and iv.
function aesCbc256encrypt($plainText, $keyStr) {
    if(strlen($keyStr) !== 64) {
        return -1;
    }
    
    $key = pack('H*', $keyStr);
    $iv = mcrypt_create_iv(32, MCRYPT_RAND);
    
    return array(
        'ciphered' => mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $plainText, MCRYPT_MODE_CBC, $iv),
        'iv' => $iv
    );
}

// AES CBC 256, Decrypt Encrypted Text with key string and iv.
function aesCbc256Decrypt($ciphered, $keyStr, $iv) {
    if(strlen($keyStr) !== 64) { return -1; }
    
    $key = pack('H*', $keyStr);
    
    return mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $ciphered, MCRYPT_MODE_CBC, $iv);
}
?>
