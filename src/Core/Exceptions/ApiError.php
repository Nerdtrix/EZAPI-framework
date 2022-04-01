<?php
    namespace Core\Exceptions;
    use Core\{Dictionary, Constant};
    use \Exception;

    class ApiError extends ExceptionHandler
    {
        private const EXCEPTION = "exception";
        private const LOCATION = "location";
        private const LINE = "line";


        public function __construct(string $errorMessage, int $httpCode = 400) 
        {
            #Add return type
            header(sprintf("Content-Type: %s", Dictionary::contentType["json"]));  
            
            #Add HTTP response code
            http_response_code($httpCode);

            //see this url for json structure https://jsonapi.org/examples/
            if (PRODUCTION)
            {
                $response = [
                    Constant::ERROR => [
                        Constant::CODE => $httpCode,
                        Constant::MESSAGE => $errorMessage
                    ]
                ];
            } 

            #Add extra details while not in production mode
            if (!PRODUCTION) 
            {
                $response = [
                    Constant::ERROR => [
                        Constant::CODE => $httpCode,
                        Constant::MESSAGE => $errorMessage,
                        self::EXCEPTION => get_class($this),
                        self::LOCATION => $this->getFile(),
                        self::LINE => $this->getLine()
                    ]
                ];
            }       

            #Convert array to object
            $response = json_encode($response);            

            #Validate Json
            if (json_last_error() !== JSON_ERROR_NONE) 
                throw new Exception(Constant::INVALID_JSON_FORMAT);
            
            #Return values
            exit($response);
        }
    }