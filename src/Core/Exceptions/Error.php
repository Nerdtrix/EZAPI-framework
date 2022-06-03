<?php
  namespace Core\Exceptions;
  use Core\{Dictionary, Constant};

  class Error 
  {
    private const EXCEPTION = "exception";
    private const LOCATION = "location";
    private const LINE = "line";

    
    public static function handler($ex)
    {
       #Add return type
       header(sprintf("Content-Type: %s", Dictionary::contentType["json"]));  
            
       #Add HTTP response code
       http_response_code(500);

       //see this url for json structure https://jsonapi.org/examples/
       if (PRODUCTION)
       {
           $response = [
               Constant::ERROR => [
                   Constant::CODE => 500,
                   Constant::MESSAGE => Dictionary::httpResponseCode[500]
               ]
           ];
       } 

       #Add extra details while not in production mode
       if (!PRODUCTION) 
       {
           $response = [
               Constant::ERROR => [
                   Constant::CODE => 500,
                   Constant::MESSAGE => $ex->getMessage(),
                   self::EXCEPTION => get_class($ex),
                   self::LOCATION => $ex->getFile(),
                   self::LINE => $ex->getLine()
               ]
           ];
       }  

       //save log
       
       

       #Convert array to object
       $response = json_encode($response);          
       
       #Return values
       exit($response);
    }
  }
?>