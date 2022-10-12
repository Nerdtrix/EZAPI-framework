<?php
    namespace Core;
    use Core\Exceptions\ApiError;
    use \Exception;

    interface IRequest
    {
        function data() : mixed;
        
        function response(mixed $response = "success", int $code = 200) : void;
    }

    class Request implements IRequest
    {
        
        private const INVALID_HTTP_RESPONSE_CODE = "Invalid HTTP response code";      


         #Allowed HTTP methods
         private $_allowedMethods = [
            "OPTIONS", //DO NOT REMOVE THIS VALUE
            "POST",
            "GET",
            "PUT",
            "PATCH",
            "DELETE"
        ];

        #Allowed HTTP headers
        private $_allowedHeaders = [
            "Content-Type"
        ];

        #Allowed HTTP content types
        private $_allowedContentType = [
            "application/json",
            "application/json; charset=UTF-8",
            "application/x-www-form-urlencoded"
        ];

        public function __construct()
        {
            #Inject headers on first call.
            $this->headers();
        }


        /**
         * @method headers
         * This method will validate all of the headers sent by the browser and assign our desired headers.
         */
        private function headers() : void
        {
            #Validate against empty origin values while in production mode
            if(!isset($_SERVER["HTTP_ORIGIN"]) && PRODUCTION)
            {
                throw new ApiError(Dictionary::httpResponseCode[400]);
            }

            #Bypass HTTP_ORIGIN while in development mode by assigning the local IP address to the Origin.
            if(!isset($_SERVER["HTTP_ORIGIN"]) && !PRODUCTION)
            {
                $_SERVER["HTTP_ORIGIN"] = $_SERVER["REMOTE_ADDR"];
            }
            
            #Validate origins even while in development mode.
            if (!in_array($_SERVER["HTTP_ORIGIN"], ALLOWED_ORIGINS) && !ALLOW_ANY_API_ORIGIN)
            {
                throw new ApiError(Dictionary::httpResponseCode[400]);
            }


            #Validate request method
            if(isset($_SERVER["REQUEST_METHOD"]) && !in_array($_SERVER["REQUEST_METHOD"], $this->_allowedMethods))
            { 
                #method not allowed
                throw new ApiError(Dictionary::httpResponseCode[405], 405);
            }

            #Make sure content type is present while in production mode.
            if (!isset($_SERVER["CONTENT_TYPE"]) && PRODUCTION)
            {
                throw new ApiError(Dictionary::httpResponseCode[415], 415);
            }

            #Overwrite Content type in development mode to JSON by default.
            if (!isset($_SERVER["CONTENT_TYPE"]) && !PRODUCTION)
            {
                $_SERVER["CONTENT_TYPE"] =  $this->_allowedContentType[0];
            }

            #Validate content type
            if(isset($_SERVER["CONTENT_TYPE"]) && !in_array($_SERVER["CONTENT_TYPE"], $this->_allowedContentType))
            {
                throw new ApiError(Dictionary::httpResponseCode[415], 415);
            }

            #Build header array
            $headerValues = [
                "Access-Control-Allow-Origin" => $_SERVER["HTTP_ORIGIN"],
                "Access-Control-Allow-Credentials" => "true", //The boolean value must be encapsulated within a string.
                "Access-Control-Allow-Methods" => implode(",", $this->_allowedMethods),
                "Access-Control-Allow-Headers" => implode(",", $this->_allowedHeaders),
                "Content-Type" => $_SERVER["CONTENT_TYPE"],
                "X-Powered-By" => EZENV["APP_NAME"],
                "app-version" => EZENV["APP_VERSION"]
            ];

            #Secure the connection with a valid SSL certificate on production.
            if(PRODUCTION && EZENV["ENFORCE_SSL"] && $_SERVER["SERVER_PORT"] !== "443")
            {
                throw new ApiError(Dictionary::httpResponseCode[495], 495);
            }

            #Ensure that headers are not already sent before assigning new headers.
            if (!headers_sent()) 
            {
                #Assign headers
                foreach($headerValues as $key => $value)
                {
                    header(sprintf("%s: %s", $key, $value));
                }
            } 

            #OPTIONS request wants only the policy, we can stop here
            if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "OPTIONS") 
            {
                exit; 
            }
        }

        private function body() : mixed
        {
            if(!empty($_POST)) return (object)$_POST;

            #Get params 
            $input = file_get_contents("php://input");

            if(empty($input)) return (object)[];

            #Decode params
            $results = json_decode($input);

            #Validate Json
            if (json_last_error() === JSON_ERROR_NONE)
            {
                return $results;
            }
            
            #Anything other than json
            return $input;     
        }


        public function data() : mixed
        {
           

            switch ($_SERVER["REQUEST_METHOD"])
            {
                case "GET" : 
                    return (object)$_GET;

                case "POST" or "PUT" or "DELETE" or "PATCH": 
                    return $this->body();

                default:
                    return null;
            }
        }


        /**
         * @method response
         * @param int code 
         * @param mixed response
         * @throws Exceptions
         */
        public function response(mixed $response = "success", int $code = 200) : void
        {
            #Validate response code
            if(!array_key_exists($code, Dictionary::httpResponseCode)) 
                throw new Exception (self::INVALID_HTTP_RESPONSE_CODE);

            #Add return type
            header(sprintf("Content-Type: %s", Dictionary::contentType["json"]));  
            
            #Add HTTP response code
            http_response_code($code);

            #convert to standard format

            if($code >= 400)
            {
                $response = [
                    Constant::SUCCESS => false,
                    Constant::CODE => $code,
                    Constant::ERROR => $response
                    
                ];
            }
            
            if($code < 400)
            {
                $response = [
                    Constant::SUCCESS => true,
                    Constant::CODE => $code,
                    Constant::PAYLOAD => $response
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
?>