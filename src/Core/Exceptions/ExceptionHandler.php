<?php
    namespace Core\Exceptions;
    use \Exception;
<<<<<<< HEAD
    use Core\Request;

    class ExceptionHandler extends Exception
    {
        public $request;

        public function __construct()
        {
            #Construct original exception class
            parent::__construct();

            $this->request = new Request();
        }
        
=======


    class ExceptionHandler extends Exception 
    {    
        public function __construct(string $message, int $code)
        {
            parent::__construct($message, $code);
        }
>>>>>>> rebuildtest
    }