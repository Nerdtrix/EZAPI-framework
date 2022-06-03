<?php
    namespace Core;

<<<<<<< HEAD

    

    class Cookie
=======
    class Cookie implements ICookie
>>>>>>> rebuildtest
    {

        /**
         * @method set
         * @param string name, value, cookieExpiration, path, domain. secure. 
         * @param boolean httpOnly
         * @return boolean
         */
<<<<<<< HEAD
        public static function set(
=======
        public function set(
>>>>>>> rebuildtest
            string $name,
            string $value,
            string $cookieExpiration, 
            string $path = "/", 
            string $domain = "", 
<<<<<<< HEAD
            bool $secure = false, //set to true in deployment
            bool $httpOnly = true ) : bool
        {
            if(!empty($name) && !empty($value) && !empty($cookieExpiration)) 
            {
                #name, value, expire, path, domain, secure, httponly

                if(setcookie($name, $value, [
                    'expires' => $cookieExpiration,
                    'path' => $path,
                    'domain' => $domain,
                    'secure' => $secure,
                    'httponly' => $httpOnly,
                    'samesite' => 'None' //None || Lax  || Strict
                ])) return true;
=======
            bool $secure = false, //set to true for production
            bool $httpOnly = true ) : bool
        {
            if(!empty($name) && !empty($value)) 
            {
                #name, value, expire, path, domain, secure, httponly
                $cookieValues = [
                    'path' => $path,
                    'domain' => $domain,
                    'expires' => strtotime($cookieExpiration),
                    'secure' => $secure,
                    'httponly' => $httpOnly,
                    'samesite' => 'None' //None || Lax  || Strict
                ];


                if(setcookie($name, $value, $cookieValues)) return true;
>>>>>>> rebuildtest
            }

            return false;
        }


        /**
         * @method get
         * @param string name
         * @return string
         */
<<<<<<< HEAD
        public static function get(string $name) : ?string 
=======
        public  function get(string $name) : ?string 
>>>>>>> rebuildtest
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
<<<<<<< HEAD
        public static function exists(string $name) : bool 
=======
        public function exists(string $name) : bool 
>>>>>>> rebuildtest
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
<<<<<<< HEAD
        public static function delete(string $name, int $expire =  365 * 24 * 60 * 60) : bool
=======
        public function delete(string $name) : bool
>>>>>>> rebuildtest
        {
            if(!empty($name) && self::exists($name))
            {
                //Set cookie in pass time
                if(setcookie($name, "", [
<<<<<<< HEAD
                    'expires' => time() + $expire,
=======
                    'expires' => 0,
>>>>>>> rebuildtest
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
<<<<<<< HEAD
        public static function deleteAll(string $skip = null) : void
        {
            #A year ago
            $expiredTime = time() - 365 * 24 * 60 * 60;

=======
        public function deleteAll(string $skip = null) : void
        {
>>>>>>> rebuildtest
            #delete all except skip
            foreach ($_COOKIE as $key => $value )
            {
                if($key != $skip)
                {
<<<<<<< HEAD
                    setcookie($key, $value, $expiredTime, '/' );
                }
            }
        }

=======
                    setcookie($key, $value, 0, '/' );
                }
            }
        }
>>>>>>> rebuildtest
    }