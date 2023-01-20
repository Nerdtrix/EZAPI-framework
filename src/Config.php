<?php
    namespace Src;

    class Config
    {
        
        public function load() : void
        {
            define("SLASH", DIRECTORY_SEPARATOR);
            define("ROOT_DIR", dirname(__DIR__, 1));
            define("PUBLIC_DIR", ROOT_DIR . SLASH . "public");
            define("SRC_DIR", dirname(__FILE__));

            define("DEFAULT_ROUTE_DIRECTORY", "Controllers");
            define("DEFAULT_ROUTE", "User");

            #Header configurations
            define("ALLOW_ANY_API_ORIGIN", true);
            define("ALLOWED_ORIGINS", [""]); #This is only required if the ALLOW_ANY_API_ORIGIN is false.

            
            #Define timezone
            define('ONE_SECOND', 1);
            define('ONE_MINUTE', 60 * ONE_SECOND);
            define('ONE_HOUR',   60 * ONE_MINUTE);
            define('ONE_DAY',    24 * ONE_HOUR);
            define('ONE_YEAR',  365 * ONE_DAY);
            define("DATE_FORMAT", "Y-m-d H:i:s");
            define("CURRENT_TIME", time()); //Unix timestamp in seconds
            define("CURRENT_DATE", date("Y-m-d"));
            define("TIMESTAMP", date(DATE_FORMAT));
            define("CURRENT_TIMEZONE", "America/New_York");
            date_default_timezone_set(CURRENT_TIMEZONE);

            define("PRODUCTION", false);
            define("TRANSLATE_RESPONSES", true);
        }
    }
?>