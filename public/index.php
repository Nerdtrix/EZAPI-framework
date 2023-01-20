<?php
    namespace EZAPIFRAMEWORK;
    use \Exception;
    use \TypeError;
    use \Throwable;
    use \ErrorException;
    use \Error;
    use Core\Dispatch;
    use Core\Exceptions\Error as ErrorHandler;

    /**
    *################################
    *#> Welcome to EZAPI framework <#
    *################################
    * 
    * @copyright (c) Nerdtrix LLC 2021 - Current
    * @author Name: Jerry Urena
    * @author Social links:  @jerryurenaa
    * @author email: jerryurenaa@gmail.com
    * @author website: jerryurenaa.com
    * @license MIT (included within this project)
    * 
    */

    

    try
    {
        #Handle warnings and fatal errors
        set_error_handler(function ($errno, $errstr, $errfile, $errline)
        {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);

        }, E_WARNING | E_ERROR);

        #Composer path
        $composerPath = sprintf(
            "%s%svendor%sautoload.php", 
            dirname(__DIR__, 1), 
            DIRECTORY_SEPARATOR, 
            DIRECTORY_SEPARATOR
        );

        #Verify composer autoload
        if(!file_exists($composerPath))
        {
          throw new Exception("Please run the command `composer install` within the directory.");  
        }

        require_once($composerPath);

        #Dispatch request
        (new Dispatch)->request();
    }
    catch(Exception $ex)
    {
        ErrorHandler::handler($ex);
    }
    catch(TypeError $ex)
    {
        ErrorHandler::handler($ex, 500);
    }
    catch(Throwable $ex)
    {
        ErrorHandler::handler($ex, 500);
    }
    catch(ErrorException $ex)
    {
        ErrorHandler::handler($ex, 500);
    }
    catch(Error $ex)
    {
        ErrorHandler::handler($ex, 500);
    }
?>