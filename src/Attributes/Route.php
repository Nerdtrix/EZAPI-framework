<?php
    namespace Attributes;
    use Exception;

    class Route
    {
        public function __construct(string $urlPath, string $method = "GET", string $middleware = null)
        {
            if(empty($urlPath)) throw new Exception("url path is required");

            //url match

            //method match

            //inject middleware
        }
    }
?>