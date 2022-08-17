<?php
    namespace Core;

    interface ICrypto
    {
        function randomToken(int $bytes = 64) : string;
        function AESEncrypt(string $content) : string;
        function AESDecrypt(string $content) : string;
    }

    class Crypto implements ICrypto
    {

        
        private const AES_CIPHER = "AES-256-CBC";
        private const AES_KEY = EZENV["AES_KEY"];
        private const AES_OPENSSL_OPTION = 0; // zero is default but can be OPENSSL_RAW_DATA or OPENSSL_ZERO_PADDING 


        /**
         * @param int bytes
         * @return string
         */
        public function randomToken(int $bytes = 64) : string
        {
            return bin2hex(openssl_random_pseudo_bytes($bytes));
        }


        /**
         * @param string data
         * @return string
         */
        public function AESEncrypt(string $data) : string
        {
            return openssl_encrypt(
                $data, 
                self::AES_CIPHER, 
                $this->getAESPsw(), 
                self::AES_OPENSSL_OPTION,
                $this->getAESIV()); 
        }


        /**
         * @param string data
         * @return string
         */
        public function AESDecrypt(string $data) : string
        {
            return openssl_decrypt(
                $data, 
                self::AES_CIPHER, 
                $this->getAESPsw(), 
                self::AES_OPENSSL_OPTION, 
                $this->getAESIV());
        }

        
        private function getAESPsw() : string
        {
            return substr(hash("sha256", self::AES_KEY, true), 0, 32);
        }

        
        private function getAESIV() : string
        {
            #random 16 bytes. 
            return pack("c*", ...[
                0x0, 
                0x1, 
                0x2, 
                0x3, 
                0x4, 
                0x5, 
                0x6, 
                0x7, 
                0x8, 
                0x9, 
                0xa, 
                0xb, 
                0xc, 
                0xd, 
                0xe, 
                0xf
            ]);
        }
    }
?>