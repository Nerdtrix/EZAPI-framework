<?php
    namespace Core;
    use Src\Config;
    use Core\DI;
    use Core\Exceptions\ApiError;
    

    class Dispatch
    {
        /**
         * @method request
         * This is the entry point of our application.
         */
        public function request() : void
        {
            

            #Load app config
            (new Config)->load();

            #Load EZENV config
            (new EZENV)->load(PRODUCTION);


            #Get the path info from the browser
            $pathInfo = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['ORIG_PATH_INFO'];

            #Convert path into array and remove the last forward slash
            $request = preg_split("#/#", ltrim($pathInfo, "/"));

             /**
             * After a request is made we will get the index of the array if it is not empty
             * and try to find a class with this name. If the index is empty we will then use the 
             * default route defined in the config file. 
             * 
             * please note that Route is a folder in src and the backslash cannot be changed for any reason
             * this is case sensitive because we are autoloading the classes.
             */
            $route =  sprintf("%s\%s", 
                DEFAULT_ROUTE_DIRECTORY,
                empty($request[0]) && empty($request[1]) ?
                ucfirst(DEFAULT_ROUTE) : ucfirst($request[0])
            );

            
            /**
             * If the 2nd object in the array $request is not empty we will use that as default method.
             * If the 2nd object in the array is empty we will get the index object otherwise if both
             * index and 2nd objects are empty we will by default use index.
             */    
            $method = !empty($request[0]) && !empty($request[1]) ? $request[1] : 'index';

            #See if parameters are sent with a get request
            if (strpos($method, "?")) 
            {
                #Remove everything after the question marks since they are parameters.
                $method = strtok($method, "?");
            }
            

            /*
            * If the class is not found or if the method does not exist in that class
            * We will return error code 404.
            */
            if(!class_exists($route) || !method_exists($route, $method))
            {
               throw new ApiError(Dictionary::httpResponseCode[404], 404);
            }

             /**
             * If all validations are passed We will pass the params to the method requested
             * and trigger the method as a new instance.
             */
            $routeInstance = (new DI)->inject($route, $method);

            $requestData = (new Request)->data();

            $routeInstance->$method($requestData);
        }
    }
?>