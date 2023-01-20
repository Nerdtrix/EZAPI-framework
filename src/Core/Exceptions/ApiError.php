<?php
    namespace Core\Exceptions;
    use Core\{Dictionary, Constant};

    class ApiError 
    {
        public function __construct(mixed $errorMessage = "something_went_wrong", int $httpCode = 400)  
        {
            #Add return type
            header(sprintf("Content-Type: %s", Dictionary::contentType["json"]));  
            
            #Add HTTP response code
            http_response_code($httpCode);

            if(is_string($errorMessage))
            {
                if(@unserialize($errorMessage) !== false)
                {
                    $errorMessage = unserialize($errorMessage);
                }
            }

            #convert to standard format
            if(!is_array($errorMessage))
            {
                $errorMessage = [
                    Constant::MESSAGE => $errorMessage
                ];
            }

            $response = [
                Constant::SUCCESS => false,
                Constant::CODE => $httpCode,
                Constant::ERROR => $errorMessage
            ]; 

            #Convert array to object
            $response = json_encode($response);  
            
            #Return values
            exit($response);
        }
    }
?>