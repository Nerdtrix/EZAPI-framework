<?php
    namespace EZAPIFRAMEWORK;
    use \Exception;
<<<<<<< HEAD
    use Core\Dispatch;
    use Core\Errors\ErrorHandler;
  
=======
    use \TypeError;
    use \Error;
    use Core\Dispatch;
    use Core\Exceptions\Error as ErrorHandler;
>>>>>>> rebuildtest

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
        //Check server requirements before instantiation.

<<<<<<< HEAD

        #Composer path
        $composerPath = sprintf("%s%svendor%sautoload.php", dirname(__DIR__, 1), DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);
=======
        #Composer path
        $composerPath = sprintf(
            "%s%svendor%sautoload.php", 
            dirname(__DIR__, 1), 
            DIRECTORY_SEPARATOR, 
            DIRECTORY_SEPARATOR
        );
>>>>>>> rebuildtest

        #Verify composer autoload
        if(file_exists($composerPath))
        {
            require_once($composerPath);
        }
        else
        {
            #Native Autoload
<<<<<<< HEAD
            spl_autoload_register(function ($className)
=======
            spl_autoload_register(function (string $className)
>>>>>>> rebuildtest
            {
                $fileName = sprintf(
                    "%s%ssrc%s%s.php", 
                    dirname(__DIR__),  
                    DIRECTORY_SEPARATOR, 
                    DIRECTORY_SEPARATOR, 
                    str_replace("\\", DIRECTORY_SEPARATOR, $className)
                );

                if (file_exists($fileName))
                {
                    require ($fileName);
                }
                else
                {
                    throw new Exception(sprintf("Class not found: %s", $fileName));
                }
            });
        }

<<<<<<< HEAD
        //run middleware before dispatching


        #Dispatch request
        Dispatch::request();
=======
        #Dispatch request
        (new Dispatch)->request();
>>>>>>> rebuildtest
    }
    catch(Exception $ex)
    {
        ErrorHandler::handler($ex);
<<<<<<< HEAD
=======
    }
    catch(TypeError $ex)
    {
        ErrorHandler::handler($ex);
    }
    catch(Error $ex)
    {
        ErrorHandler::handler($ex);
>>>>>>> rebuildtest
    }