<?php
    namespace Core;

    class Crypto implements ICrypto
    {
        public function randomToken(int $bytes = 64) : string
        {
            return bin2hex(openssl_random_pseudo_bytes($bytes));
        }
    }
?>