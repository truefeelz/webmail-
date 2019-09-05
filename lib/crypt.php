<?php
define('ENCRYPT_KEY', 'p3s6v9y$B&E)H@McQfTjWnZr4t7w!z%C*F-JaNdRgUkXp2s5v8x/A?D(G+KbPeSh');//ключ шифрования
// шифрование
function encrypt($source_text){
    $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($source_text, $cipher, ENCRYPT_KEY, $options=OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext_raw, ENCRYPT_KEY, $as_binary=true);
    $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
    return $ciphertext;
}

//дешифрование
function decrypt($ciphertext){
    $c = base64_decode($ciphertext);
    $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, $sha2len=32);
    $ciphertext_raw = substr($c, $ivlen+$sha2len);
    $decrypt_text = openssl_decrypt($ciphertext_raw, $cipher, ENCRYPT_KEY, $options=OPENSSL_RAW_DATA, $iv);
    $calcmac = hash_hmac('sha256', $ciphertext_raw, ENCRYPT_KEY, $as_binary=true);
    if (hash_equals($hmac, $calcmac))
    {
        return $decrypt_text;
    }
}
?>