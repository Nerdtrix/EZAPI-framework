<?php
    namespace Core;

    class Cookie implements ICookie
    {

        /**
         * @method set
         * @param string name, value, cookieExpiration, path, domain. secure. 
         * @param boolean httpOnly
         * @return boolean
         */
        public function set(
            string $name,
            string $value,
            int $cookieExpiration, 
            string $path = "/", 
            string $domain = "", 
            bool $secure = false, //set to true for production
            bool $httpOnly = true ) : bool
        {
            if(!empty($name) && !empty($value)) 
            {
                #name, value, expire, path, domain, secure, httponly
                $cookieValues = [
                    'path' => $path,
                    'domain' => $domain,
                    'expires' => $cookieExpiration,
                    'secure' => $secure,
                    'httponly' => $httpOnly,
                    'samesite' => 'None' //None || Lax  || Strict
                ];


                if(setcookie($name, $value, $cookieValues)) return true;
            }

            return false;
        }


        /**
         * @method get
         * @param string name
         * @return string
         */
        public  function get(string $name) : ?string 
        {
            if(!empty($name) && self::exists($name))
            {
                return $_COOKIE[$name];
            }

            return null;
        }


        /**
         * @method exists
         * @param string name
         * @return string
         */
        public function exists(string $name) : bool 
        {
            if(!empty($name) && isset($_COOKIE[$name]))
            {
                return true;
            }

            return false;
        }
        

        /**
         * @method delete
         * @param string name
         * @param int expire (by default 1 year ago)
         * @return boolean
         */
        public function delete(string $name) : bool
        {
            if(!empty($name) && self::exists($name))
            {
                //Set cookie in pass time
                if(setcookie($name, "", [
                    'expires' => 0,
                    'path' => "/",
                    'domain' => "",
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'None',
                ])) return true;
            }

            return false;
        }

        
        /**
         * @method deleteAll
         * @param string skip
         * @comment this method will delete all the cookies. 
         * IF the param skip is not null it will skip the specified cookie if it exists.
         */
        public function deleteAll(string $skip = null) : void
        {
            #delete all except skip
            foreach ($_COOKIE as $key => $value )
            {
                if($key != $skip)
                {
                    setcookie($key, $value, 0, '/' );
                }
            }
        }
    }