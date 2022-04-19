<?php
    namespace Core\Database\Mysql;
    use \PDO;
    use \PDOException;
    use Core\Exceptions\Error;

    class Connection
    {
        public function __construct()
        {
            try 
            { 
                $config = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_CASE => PDO::CASE_NATURAL,
                    PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING,
                    PDO::MYSQL_ATTR_INIT_COMMAND => sprintf(
                        "SET NAMES %s;SET time_zone ='%s'", 
                        "utf8", 
                        CURRENT_TIMEZONE
                    )#set timezone to match the default framework timezone
                ];

               return new PDO(sprintf(
                    "mysql:host=%s;dbname=%s", 
                    EZENV['DB_HOST'], 
                    EZENV['DB_NAME']),  
                    EZENV['DB_USER'], 
                    EZENV['DB_PASSWORD'],
                    $config
                );
            }
            catch(PDOException $ex)
            {
                Error::handler($ex);
            }
        }
    }
?>