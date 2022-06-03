<?php
    namespace Core;

    class Helper implements IHelper
    {
        /**
         * @method publicIP
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


         /**
         * @param int length
         * @return int 
         */
        public function randomNumber(int $length) : int
        {
            $result = "";

            for($i = 0; $i < (int) $length; $i++) 
            {
                $result .= mt_rand(0, 9);
            }

            return (int)$result;
        }


        /**
         * @return array [name, platform]
         */
        public function getBrowserInfo() : object
        {

            /**
             * If the browscap.ini is properly configured we will obtain our 
             * info from there since it is more accurate, otherwise we will 
             * manually identify the browser.
             */
            if (!ini_get('browscap')) 
            {
                $browserInfo = get_browser();

                if(!empty($browserInfo->browser) && $browserInfo->browser !== "Default Browser" 
                && !empty($browserInfo->platform) && $browserInfo->platform !== "unknown")
                {
                    return (object)[
                        "name" => $browserInfo->browser,
                        "platform" => $browserInfo->platform
                    ];
                }
            }


            $platform = "Unknown";
            $browserName = "Unknown";

            $userAgent = $_SERVER["HTTP_USER_AGENT"];

            $operatingSystem = [
                '/linux/i'              => "Linux",
                '/macintosh|mac os x/i' => "Mac OS",
                '/windows|win32/i'      => "Windows",
                '/ubuntu/i'             => 'Ubuntu',
                '/iphone/i'             => 'iPhone',
                '/ipod/i'               => 'iPod',
                '/ipad/i'               => 'iPad',
                '/android/i'            => 'Android',
                '/blackberry/i'         => 'BlackBerry',
                '/webos/i'              => 'Mobile'
            ];

            $browserList = [
                '/msie/i'       => "Internet explorer",
                '/IE/i'         => "Internet explorer",
                '/Edg/i'        => "Microsoft Edge",
                '/gecko/i'      => "Mozilla Firefox",
                '/fox/i'        => "Mozilla Firefox",
                '/safari/i'     => "Safari",
                '/opera/i'      => "Opera",
                '/Presto/i'     => "Opera",
                '/mobile/i'     => "Mobile browser",
                '/phone/i'      => "Mobile browser",
                '/Yowser/i'     => "Yandex Browser",
                '/Ya/i'         => "Yandex Browser",
                '/Chrome/i'     => "Google Chrome",

                #Debugging tool
                '/PostmanRuntime/i' => 'Postman'
            ];

            foreach($operatingSystem as $osKey => $osValue)
            {
                if (preg_match($osKey, $userAgent)) 
                {
                    $platform = $osValue;
                }
            }           

            foreach($browserList as $bKey => $bValue)
            {
                if (preg_match($bKey, $userAgent)) 
                {
                    $browserName = $bValue;
                }
            }   
           
            return (object)[
                "name" => $browserName,
                "platform" => $platform
            ];
        }
    }
?>