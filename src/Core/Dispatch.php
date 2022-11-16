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

             #See if parameters are sent with a get request and remove them
             if (strpos($pathInfo, "?")) 
             {
                 #Remove everything after the question marks since they are parameters.
                 $pathInfo = strtok($pathInfo, "?");
             }

            #Convert path into array and remove the last forward slash
            $request = preg_split("#/#", ltrim($pathInfo, "/"));

            #Request data from GET, POST etc. This also assigns the necessary browser headers. 
            $requestData = (new Request)->data();
            
            /**
             * index is the default method called unless specified in the URL.
             * 
             * if the array has 2 or more items we will then get the last item as the method name.
             * After getting the last item as the method name, we remove the last item from 
             * the array since it is no longer needed.
             */    
            $method = "index";
            
            if(!empty($request[0]) && !empty($request[1]))
            {
                $method = end($request);

                #remove the last element since it is the method and we already got it in the previous step.
                array_pop($request);
            }
           

            /**
            * If no controller is specified in the URL we will use the default controller.
            * If 2 or more items are present in the array then we will parse the nested controller classes.
            * 
            * default route defined in the config file. 
            * please note that Backslash cannot be changed for any reason because this is a class mapping function and 
            * it is case sensitive because of the autoloading classes.
            */
            $route = ucfirst(DEFAULT_ROUTE);

            if(!empty($request[0]) && $request[0] != $route)
            {
                $nestedPath = null;

                foreach($request as $requestName)
                {
                    $nestedPath .= sprintf("\%s", ucfirst($requestName));
                }

                $route = $nestedPath;

                $route = ltrim($route, "#\#");//Remove the first backslash to prevent duplication
            }

            #build the class path
            $route =  sprintf("%s\%s", DEFAULT_ROUTE_DIRECTORY, $route);

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

            #Execute method and inject its assigned data.
            $routeInstance->$method(
                $requestData, 
                $_SERVER["REQUEST_METHOD"]
            );
        }
    }
?>