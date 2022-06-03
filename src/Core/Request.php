<?php
    namespace Core;
<<<<<<< HEAD
    use \Exception;
    use Core\Constant;
    use Core\Helper;
    use Core\Dictionary;
    use Core\Exceptions\ApiError;

    class Request
    {

        #Allowed HTTP methods
        private $_allowedMethods = [
=======
    use Core\Exceptions\ApiError;
    use \Exception;

    class Request implements IRequest
    {
        
        private const INVALID_HTTP_RESPONSE_CODE = "Invalid HTTP response code";      


         #Allowed HTTP methods
         private $_allowedMethods = [
>>>>>>> rebuildtest
            "POST",
            "GET",
            "PUT",
            "PATCH",
<<<<<<< HEAD
            "DELETE",
            "OPTIONS"
=======
            "DELETE"
>>>>>>> rebuildtest
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


        /**
         * @method headers
         * This method will validate all of the headers sent by the browser and assign our desired headers.
         */
<<<<<<< HEAD
        public function headers() : void
        {
            #Validate against empty origin values while in production mode
            if(!isset($_SERVER["HTTP_ORIGIN"]) && EZENV["PRODUCTION"])
            {
                print_r($_SERVER["REMOTE_ADDR"]); die;
                $this->jsonResponse(400, [Constant::ERROR => Dictionary::httpResponseCode[400]]);
            }

            #Bypass HTTP_ORIGIN while in development mode by assigning the local IP address to the Origin.
            if(!isset($_SERVER["HTTP_ORIGIN"]) && !EZENV["PRODUCTION"])
=======
        private function headers() : void
        {
            #Validate against empty origin values while in production mode
            if(!isset($_SERVER["HTTP_ORIGIN"]) && PRODUCTION)
            {
                throw new ApiError(Dictionary::httpResponseCode[400]);
            }

            #Bypass HTTP_ORIGIN while in development mode by assigning the local IP address to the Origin.
            if(!isset($_SERVER["HTTP_ORIGIN"]) && !PRODUCTION)
>>>>>>> rebuildtest
            {
                $_SERVER["HTTP_ORIGIN"] = $_SERVER["REMOTE_ADDR"];
            }
            
            #Validate origins even while in development mode.
            if (!in_array($_SERVER["HTTP_ORIGIN"], ALLOWED_ORIGINS) && !ALLOW_ANY_API_ORIGIN)
            {
<<<<<<< HEAD
                $this->jsonResponse(400, [Constant::ERROR => Dictionary::httpResponseCode[400]]);
=======
                throw new ApiError(Dictionary::httpResponseCode[400]);
>>>>>>> rebuildtest
            }

            #Validate request method
            if(isset($_SERVER["REQUEST_METHOD"]) && !in_array($_SERVER["REQUEST_METHOD"], $this->_allowedMethods))
            { 
                #method not allowed
<<<<<<< HEAD
                $this->jsonResponse(405, [Constant::ERROR => Dictionary::httpResponseCode[405]]);
            }

            #Make sure content type is present while in production mode.
            if (!isset($_SERVER["CONTENT_TYPE"]) && EZENV["PRODUCTION"])
            {
                $this->jsonResponse(415, [Constant::ERROR => Dictionary::httpResponseCode[415]]);
            }

            #Overwrite Content type in development mode to JSON by default.
            if (!isset($_SERVER["CONTENT_TYPE"]) && !EZENV["PRODUCTION"])
=======
                throw new ApiError(Dictionary::httpResponseCode[405], 405);
            }

            #Make sure content type is present while in production mode.
            if (!isset($_SERVER["CONTENT_TYPE"]) && PRODUCTION)
            {
                throw new ApiError(Dictionary::httpResponseCode[415], 415);
            }

            #Overwrite Content type in development mode to JSON by default.
            if (!isset($_SERVER["CONTENT_TYPE"]) && !PRODUCTION)
>>>>>>> rebuildtest
            {
                $_SERVER["CONTENT_TYPE"] =  $this->_allowedContentType[0];
            }

            #Validate content type
            if(isset($_SERVER["CONTENT_TYPE"]) && !in_array($_SERVER["CONTENT_TYPE"], $this->_allowedContentType))
            {
<<<<<<< HEAD
                die($_SERVER["CONTENT_TYPE"]);
                $this->jsonResponse(415, [Constant::ERROR => Dictionary::httpResponseCode[415]]);
=======
                throw new ApiError(Dictionary::httpResponseCode[415], 415);
>>>>>>> rebuildtest
            }

            #Build header array
            $headerValues = [
                "Access-Control-Allow-Origin" => $_SERVER["HTTP_ORIGIN"],
                "Access-Control-Allow-Credentials" => true,
                "ccess-Control-Allow-Methods" => implode(",", $this->_allowedMethods),
                "Access-Control-Allow-Headers" => implode(",", $this->_allowedHeaders),
                "Content-Type" => $_SERVER["CONTENT_TYPE"],
                "X-Powered-By" => EZENV["APP_NAME"],
                "app-version" => EZENV["APP_VERSION"]
            ];

            #Secure the connection with a valid SSL certificate on production.
<<<<<<< HEAD
            if(EZENV["PRODUCTION"] && EZENV["ENFORCE_SSL"] && $_SERVER["SERVER_PORT"] !== "443")
            {
                $this->response(495,  [Constant::ERROR => Dictionary::httpResponseCode[495]]);
=======
            if(PRODUCTION && EZENV["ENFORCE_SSL"] && $_SERVER["SERVER_PORT"] !== "443")
            {
                throw new ApiError(Dictionary::httpResponseCode[495], 495);
>>>>>>> rebuildtest
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
        }

<<<<<<< HEAD

         /**
         * @method response
         * @param int code 
         * @param array response
         * @param string contentType 
         * @return object
         * @throws Exceptions
         * @see Core\Dictionary for the list of content types  or http response codes
         */
        public function jsonResponse(int $code, array $response, string $contentType = "json") : void
        {

            #Validate response code
            if(!array_key_exists($code, Dictionary::httpResponseCode)) throw new Exception (Constant::INVALID_HTTP_RESPONSE_CODE);

            #Validate content type
            if(!array_key_exists($contentType, Dictionary::contentType)) throw new Exception (Constant::INVALID_CONTENT_TYPE);

            #Add return type Json
            header(sprintf("Content-Type: %s", Dictionary::contentType[$contentType]));  
            
            #Add HTTP response code
            http_response_code($code);

            #Convert array to object
            if($contentType === "json")
            {
                #Encode values
                $response = json_encode($response);
            }
            
            #Return values
            exit($response);
        }



        /**
         * @method inputJson
         * @param bool sanitize (obtional) false by default
         * @return object
         * @throws Exception
         */
        public function jsonInput(bool $sanitize = false) : object
        {
=======
        private function body() : mixed
        {
            if(!empty($_POST)) return (object)$_POST;

>>>>>>> rebuildtest
            #Get params 
            $input = file_get_contents("php://input");

            #Decode params
            $results = json_decode($input);

            #Validate Json
<<<<<<< HEAD
            if (json_last_error() !== JSON_ERROR_NONE) 
            {
                throw new ApiError(Constant::INVALID_JSON_FORMAT);
            }

            #Sanitize values
            if($sanitize)
            {
                $results = Helper::sanitizeObject($results);
            }

            #Return object
            return $results;
        }


        /**
         * 
         */
        public function getInput(bool $sanitize = false) : object
        {
            if(!isset($_GET) || empty($_GET))
            {
                throw new ApiError(Constant::INVALID_GET_PARAMETERS);
            }

            $inputGet = $_GET;

            if($sanitize)
            {
                $inputGet = Helper::sanitizeGet($inputGet);
            }

            return (object)$inputGet;
=======
            if (json_last_error() === JSON_ERROR_NONE)
            {
                return $results;
            }
            
            #Anything other than json
            return $input;     
        }


        public function data() : mixed
        {
            #Assign headers
            $this->headers();

            switch ($_SERVER["REQUEST_METHOD"])
            {
                case "GET" : 
                    return (object)$_GET;

                case "POST" or "PUT" or "DELETE" or "PATCH": 
                    return $this->body();

                default:
                    return null;

            }
>>>>>>> rebuildtest
        }


        /**
<<<<<<< HEAD
         * 
         */
        public function postInput(bool $sanitize = false) : object
        {
            if(!isset($_POST) || empty($_POST))
            {
                throw new ApiError(Constant::INVALID_POST_PARAMETERS);
            }

            $inputPost = $_POST;

            if($sanitize)
            {
                $inputPost = Helper::sanitizePost($inputPost);
            }

            return (object)$inputPost;
        }
=======
         * @method response
         * @param int code 
         * @param mixed response
         * @throws Exceptions
         */
        public function response(mixed $response, int $code = 200) : void
        {
            #Validate response code
            if(!array_key_exists($code, Dictionary::httpResponseCode)) 
                throw new Exception (self::INVALID_HTTP_RESPONSE_CODE);

            #Add return type
            header(sprintf("Content-Type: %s", Dictionary::contentType["json"]));  
            
            #Add HTTP response code
            http_response_code($code);

            //see this url for json structure https://jsonapi.org/examples/
            if($code >= 400)
            {
                $response = [
                    Constant::ERROR => [
                        Constant::CODE => $code,
                        Constant::MESSAGE => $response
                    ]
                ];
            }
            
            if($code == 200)
            {
                $response = [
                    Constant::RESULT => $response
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

>>>>>>> rebuildtest
    }