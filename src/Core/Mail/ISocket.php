<?php
    namespace Core\Mail;

    define("PHP_CRLF", "\r\n");

    interface ISocket
    {
        public function open(string $host, int $port, float $timeout) : void;
        public function readString(int $lenToRead) : string;
        public function writeString(string $data) : void;
        public function enableCrypto() : void;
        public function close() : void;
    }
?>