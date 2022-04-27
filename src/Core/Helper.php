<?php
    namespace Core;


    class Helper implements IHelper
    {
        /**
         * @method publicIP
         * @return string
         */
        public function publicIP() : string
        {
            $realIP = "Invalid IP Address";

            $activeHeaders = [];

            $headers = [
                "HTTP_CLIENT_IP",
                "HTTP_PRAGMA",
                "HTTP_XONNECTION",
                "HTTP_CACHE_INFO",
                "HTTP_XPROXY",
                "HTTP_PROXY",
                "HTTP_PROXY_CONNECTION",
                "HTTP_VIA",
                "HTTP_X_COMING_FROM",
                "HTTP_COMING_FROM",
                "HTTP_X_FORWARDED_FOR",
                "HTTP_X_FORWARDED",
                "HTTP_X_CLUSTER_CLIENT_IP",
                "HTTP_FORWARDED_FOR",
                "HTTP_FORWARDED",
                "ZHTTP_CACHE_CONTROL",
                "REMOTE_ADDR" #this should be the last option
            ];

            #Find active headers
            foreach ($headers as $key)
            {
                if (array_key_exists($key, $_SERVER))
                {
                    $activeHeaders[$key] = $_SERVER[$key];
                }
            }

             #Reemove remote address since we got more options to choose from
            if(count($activeHeaders) > 1 && isset($_SERVER["REMOTE_ADDR"]))
            {
                unset($activeHeaders["REMOTE_ADDR"]);
            }

            #Pick a random item now that we have a secure way.
            $realIP = $activeHeaders[array_rand($activeHeaders)];

            #Validate the public IP
            if (filter_var($realIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
            {
                return $realIP;
            }

            return $realIP;
        }


        public function randomToken()   : string
        {
            $randBuff = openssl_random_pseudo_bytes(128);
            
            return base64_encode($randBuff);
        }
    }