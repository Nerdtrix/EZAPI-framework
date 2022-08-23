<?php
    namespace Core\Database\Mysql;
    use \PDO;
    use \PDOException;
    use \Exception;
    use Core\Exceptions\Error;

    interface IMysql
    {
        function db() : \PDO;

        function select(string $query, array $bind, string $model = null, string $fetchMode = \PDO::FETCH_OBJ) : object;

        function insert(string $query, array $bind) : int;

        function lastInsertId() : int;

        function update(string $query, array $bind) : bool;

        function delete(string $query, array $bind) : bool;

        const DATE_FORMAT = "Y-m-d";

        const DATE_TIME_FORMAT = "Y-m-d H:i:s";
    }

    class Mysql implements IMysql
    {
        protected PDO $m_db;

        public const DATE_FORMAT = "Y-m-d";
        public const DATE_TIME_FORMAT = "Y-m-d H:i:s";
        

        public function __construct() 
        {
            $this->connect();
        }

        
        /**
         * @param string query
         * @param array bind
         * @param string model
         * @param string fetchMode (optional)
         * @return object
         */
        public function select(string $query, array $bind, string $model = null, string $fetchMode = PDO::FETCH_OBJ) : object
        {
            #validate data
            if(empty($query)) throw new Exception("A query is required");
            if(empty($bind)) throw new Exception("you must bind your data");
            if(!class_exists($model)) throw new Exception("the model class is invalid or does not exists");

            

            #prepare the query
            $stmt = $this->m_db->prepare($query);

            for ($i = 1; $i <= count($bind); $i++)
            {
                $stmt->bindValue($i, $bind[$i - 1]);
            }

            $stmt->setFetchMode($fetchMode); 

            if(!$stmt->execute())
            {
                throw new Exception($stmt->error);
            }

            
            if($stmt->rowCount() <= 1)
            {
                $results = $stmt->fetch();

                if(is_null($model))
                {
                    return $results;
                }

                #new Instance of a model
                $modelObject = new $model();

                #return empty object on bool
                if(is_bool($results))
                {
                    return $modelObject;
                }
                

                #Propagate results
                foreach ($results as $key => $value) 
                {
                    #only assign properties that are declared
                    if (property_exists($modelObject, $key)) 
                    {
                        $modelObject->$key = $value;
                    }
                }

                return $modelObject;
            }
            else
            {
                $results = $stmt->fetchAll();

                if(is_null($model))
                {
                    return $results;
                }

                $data = [];

                foreach($results as $result)
                {
                    #new Instance of a model
                    $modelObject = new $model();

                    #Propagate results
                    foreach ($result as $key => $value) 
                    {
                        #only assign properties that are declared
                        if (property_exists($modelObject, $key)) 
                        {
                            $modelObject->$key = $value;
                        }
                    }

                    #Build array
                    array_push($data, $modelObject);
                }

                return (object)$data;
            }
        }


        /**
         * @param string query
         * @param array bind
         * @return int
         * @throws Exceptions
         */
        public function insert(string $query, array $bind) : int
        {
            $stmt = $this->m_db->prepare($query);

            for ($i = 1; $i <= count($bind); $i++)
            {
                $stmt->bindValue($i, $bind[$i - 1]);
            }

            if(!$stmt->execute())
            {
                throw new Exception($stmt->error);
            }

            return $this->m_db->lastInsertId();
        }


        /**
         * @param string query
         * @param array bind
         * @return bool
         * @throws Exceptions
         */
        public function update(string $query, array $bind) : bool
        {
            #Prepare
            $stmt = $this->m_db->prepare($query);

            #Bind data
            for ($i = 1; $i <= count($bind); $i++)
            {
                $stmt->bindValue($i, $bind[$i - 1]);
            }

            if(!$stmt->execute())
            {
                throw new Exception($stmt->error);
            }

            return true;
        }


        /**
         * @param string query
         * @param array bind
         * @return bool
         * @throws Exceptions
         */
        public function delete(string $query, array $bind) : bool
        {
            #Prepare
            $stmt = $this->m_db->prepare($query);

            #Bind data
            for ($i = 1; $i <= count($bind); $i++)
            {
                $stmt->bindValue($i, $bind[$i - 1]);
            }

            if(!$stmt->execute())
            {
                throw new Exception($stmt->error);
            }

            return true;
        }


        /**
         * @return PDO
         */
        public function db() : PDO
        {
            return $this->m_db;
        }


        /**
         * @return int
         */
        public function lastInsertId() : int
        {
            return $this->m_db->lastInsertId();
        }
        

 

        /**
         * Create a PDO database connection.
         */
        private function connect() : void 
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

                $this->m_db = new PDO(sprintf(
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