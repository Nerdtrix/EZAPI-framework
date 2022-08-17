<?php
    namespace Core\Exceptions;
    use Core\{Dictionary, Constant};
    use \Exception;

    class ApiError extends Exception 
    {
        public function __construct(mixed $errorMessage, int $httpCode = 400) 
        {
            parent::__construct($errorMessage, $httpCode);

            #Add return type
            header(sprintf("Content-Type: %s", Dictionary::contentType["json"]));  
            
            #Add HTTP response code
            http_response_code($httpCode);
            
            $response = [
                Constant::STATUS => Constant::ERROR,
                Constant::CODE => $httpCode,
                Constant::ERRORS => $errorMessage
            ]; 

            #Convert array to object
            $response = json_encode($response);  
            
            #Return values
            exit($response);
        }
    }
?>