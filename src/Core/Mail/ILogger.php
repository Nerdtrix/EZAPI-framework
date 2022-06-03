<?php
    namespace Core\Mail;

    interface ILogger
    {
        public function log(string $format, ...$values) : void;
    }

?>