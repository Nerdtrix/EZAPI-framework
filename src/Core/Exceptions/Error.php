<?php
  namespace Core\Exceptions;
  use Core\{Dictionary, Constant};

  class Error 
  {
    private const EXCEPTION = "exception";
    private const LOCATION = "location";
    private const LINE = "line";

    
    public static function handler(mixed $ex, int $errorCode = 400) : void
    {
       #Add return type
       header(sprintf("Content-Type: %s", Dictionary::contentType["json"]));  
            
       #Add HTTP response code
       http_response_code($errorCode);

       if(is_string($ex))
        {
          if(@unserialize($ex) !== false)
          {
            $ex = unserialize($ex);
          }
        }
      
       #Add extra details while not in production mode
       if (!PRODUCTION) 
       {
          $response = [
            Constant::SUCCESS => false,
            Constant::CODE => $errorCode,
            Constant::ERRORS => [
              Constant::MESSAGE => $ex->getMessage(),
              self::EXCEPTION => get_class($ex),
              self::LOCATION => $ex->getFile(),
              self::LINE => $ex->getLine()
            ]
          ];
       } 
       else
       {
        $errorCode = $errorCode < 500 ? $ex->getMessage() : Dictionary::httpResponseCode[500];

          $response = [
            Constant::SUCCESS => false,
            Constant::CODE => $errorCode,
            Constant::ERRORS => [
              Constant::MESSAGE => $errorCode
            ]
          ];
       } 

       //todo save log

       #Convert array to object
       $response = json_encode($response);          
       
       #Return values
       exit($response);
    }
  }
?>