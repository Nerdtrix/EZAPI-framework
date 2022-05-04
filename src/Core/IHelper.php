<?php
    namespace Core;

    interface IHelper
    {
        function publicIP() : string;

        function randomNumber(int $length) : int;
    }
?>