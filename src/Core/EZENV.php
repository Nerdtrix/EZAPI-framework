<?php
    namespace Core;
    use \SplFileObject;
<<<<<<< HEAD
    use Core\Helper;
=======
>>>>>>> rebuildtest

    class EZENV
    {
        /**
         * @method load
         * EZENV is one of many unique features that EZAPI offers. 
         * With this feature you are able to use enviroment variables natively.
         */
<<<<<<< HEAD
        public static function load() : void
        {
            #use the production .env by default.
            $fileName = ".env.production";
            $production = true; #it is always a good idea to know in which mode we are working.

            $localhost = [
                "127.0.0.1",
                "::1"
            ];

            #Get public IP
            $publicIP = Helper::publicIP();

            #Get local ip
            $localIP = getHostByName(getHostName());

            /**
             * While using xammp the ip address is usually 127.0.0.1 or ::1 but while using docker 
             * the ip address becomes something like this 192.168.176.1 and the http request also has a different
             * local ip number at the end. for this reason to validate the ip address in docker we remove the last .number
             * example in xampp would be the same public and local ip 127.0.0.1 but in docker it would be 192.168.176.x and
             * 192.168.176.+x for this reason we use a substring validation method to be able to support both enviroments.
             */
            if(in_array($publicIP, $localhost) || substr($publicIP, 0, -2) === substr($localIP, 0, -2))
            {
                $fileName = ".env.development";

                $production = false;
            }
            

            #Open the .env file and read it
            $dotEnvFile = new SplFileObject(
                sprintf("%s%s%s",
                    ROOT_DIR,
                    SLASH,
                    $fileName
                )
            );

=======
        public function load(bool $isProduction) : void
        {
            #Open the .env file and read it
            $filePath = sprintf("%s%s.env", ROOT_DIR, SLASH);
            
            #Recreate the files if they don't exists
            if(!file_exists($filePath))
            {   
                $defaultFilePath = sprintf("%s%s.env.example", ROOT_DIR, SLASH);

                $defaultContent = file_get_contents($defaultFilePath);

                file_put_contents($filePath , $defaultContent);
            }

            $dotEnvFile = new SplFileObject($filePath);
>>>>>>> rebuildtest

            #contains the parse values
            $env = [];

            #read .env line by line
            while (!$dotEnvFile->eof()) 
            {
                #read line
                $line = $dotEnvFile->fgets();

                #Skip line with the # symbol
                if(substr($line, 0, 1) === "#") continue;

                #Skip line with two forward slash
                if(substr($line, 0, 1) === "//") continue;

                #Skip empty lines
                if(empty($line)) continue;

                #Skip lines without the equal sign
                if(!strpos($line, "=")) continue;

                #Convert values to array
                $splitEnv = explode("=", $line);

                #Remove white spaces
                $splitEnv[1] = trim($splitEnv[1]);

                #If the value contains string quotes remove them.
                if(substr($splitEnv[1], 0, 1) === '"')
                {
                    $splitEnv[1] = substr($splitEnv[1], 1, -1);
                }

                #add key and value to the env array
                $env[trim($splitEnv[0])] = $splitEnv[1];
            }

            #Merge arrays
<<<<<<< HEAD
            $env = array_merge($env, ["PRODUCTION" => $production]);
=======
            $env = array_merge($env, ["PRODUCTION" => $isProduction]);
>>>>>>> rebuildtest
          
            #Add env variables
            foreach ($env as $key => $value) 
            {
                putenv(sprintf("%s=%s", $key, $value));
            }

            #Create EZENV function and return its value when called.
            if(!function_exists("EZENV")) 
            {
                function EZENV($key)
                {
                    $value = getenv($key);
          
                    if ($value === false) 
                    {
<<<<<<< HEAD
                        return "Invalid env key";
=======
                        return $key;
>>>>>>> rebuildtest
                    }
          
                    return $value;
                }
            }

            #Define the global EZENV global variable to be used as alternative way.
            define("EZENV", $env);
        }
    }