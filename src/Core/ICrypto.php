<?php
    namespace Core;

    interface ICrypto
    {
        function randomToken(int $bytes = 64) : string;
    }
?>