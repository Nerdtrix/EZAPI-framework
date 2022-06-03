<?php
    namespace Core\Mail;

    interface IMailIdGenerator
    {
        public function generate() : string;
    }
?>
