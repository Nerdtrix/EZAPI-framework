<?php
    namespace Core;

    class Crypto implements ICrypto
    {
        public function randomToken() : string
        {
            return base64_encode(openssl_random_pseudo_bytes(32));
        }
    }
?>