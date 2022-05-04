<?php
    namespace Core;

    interface ICookie
    {
        function set(
            string $name,
            string $value,
            int $cookieExpiration, 
            string $path = "/", 
            string $domain = "", 
            bool $secure = false, 
            bool $httpOnly = true ) : bool;


        function get(string $name) : ?string;

        function exists(string $name) : bool;

        function delete(string $name) : bool;

        function deleteAll(string $skip = null) : void;
    }
?>