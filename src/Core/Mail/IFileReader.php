<?php

namespace Core\Mail;

    interface IFileReader
    {
        public function read(string $path) : string;
    }