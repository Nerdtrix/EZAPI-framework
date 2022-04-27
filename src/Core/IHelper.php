<?php
    namespace Core;

    interface IHelper
    {
        function publicIP() : string;

        function randomToken()   : string;

    }
?>