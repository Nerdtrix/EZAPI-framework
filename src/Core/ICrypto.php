<?php
    namespace Core;

    interface ICrypto
    {
        function randomToken() : string;
    }
?>