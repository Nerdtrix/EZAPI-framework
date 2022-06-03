<?php
    namespace Core;

    interface IRequest
    {
        function data() : mixed;
        
        function response(mixed $response, int $code = 200) : void;
    }
?>