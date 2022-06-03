<?php
    namespace Core\Exceptions;
    use \Exception;


    class ExceptionHandler extends Exception 
    {    
        public function __construct(string $message, int $code)
        {
            parent::__construct($message, $code);
        }
    }
?>