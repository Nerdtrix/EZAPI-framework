<?php
    namespace Core\Mail;

    interface IMailBuilderWriter
    {
        public function writeHeader(string $data) : void;
        public function writeBody(string $data) : void;
    }
?>