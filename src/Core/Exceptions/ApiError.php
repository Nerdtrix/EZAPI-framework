<?php
    namespace Core\Exceptions;
    use Core\{Dictionary, Constant};

    class ApiError 
    {
        public function __construct(mixed $errorMessage, int $httpCode = 400) 
        {
            #Add return type
            header(sprintf("Content-Type: %s", Dictionary::contentType["json"]));  
            
            #Add HTTP response code
            http_response_code($httpCode);

            #convert to standard format
            if(!is_array($errorMessage))
            {
                $errorMessage = [
                    Constant::MESSAGE => $errorMessage
                ];
            }

            if(is_array($errorMessage))
            {
                if(!array_key_exists(0, $errorMessage))
                {
                    $errorMessage = [$errorMessage];
                }
            }

            $response = [
                Constant::STATUS => Constant::ERROR,
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