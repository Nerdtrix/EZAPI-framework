<?php
    namespace Core\Database\Mysql;

    use Exception;
    use \PDO;

    class Mysql implements IMysql
    {
        protected PDO $m_db;
        protected  string $query = "";

        public function __construct(Connection $connection) 
        {
            $this->m_db = $connection->start();
        }
        
        /**
         * @param string query
         * @param array bind
         * @param string model
         * @param string fetchMode (optional)
         * @return object
         */
        public function select(string $query, array $bind, string $model, string $fetchMode = PDO::FETCH_OBJ) : object
        {
            #validate data
            if(empty($query)) throw new Exception("A query is required");
            if(empty($bind)) throw new Exception("you must bind your data");
            if(empty($model)) throw new Exception("A data model is required");
            if(!class_exists($model)) throw new Exception("the model class is invalid");

            $data = [];

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

            if($stmt->rowCount() == 1)
            {
                $results = $stmt->fetch();

                #new Instance of a model
                $modelObject = new $model();

                #Propagate results
                foreach ($results as $key => $value) 
                {
                    #only assign properties that are declared
                    if (property_exists($modelObject, $key)) 
                    {
                        $modelObject->$key = $value;
                    }
                }

                $data = $modelObject;
            }
            else
            {

                $results = $stmt->fetchAll();

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
            }
           
            return (object)$data;
        }

        public function insert(string $query, array $bind) : int
        {
            $stmt = $this->m_db->prepare($query);

            for ($i = 1; $i <= count($bind); $i++)
            {
                $stmt->bindValue($i, array_values($bind));
            }

            if(!$stmt->execute())
            {
                throw new Exception($stmt->error);
            }

            return $this->m_db->lastInsertId();
        }


        public function update(string $query, array $bind) : bool
        {
            #Prepare
            $stmt = $this->m_db->prepare($query);

            #Bind data
            for ($i = 1; $i <= count($bind); $i++)
            {
                $stmt->bindValue($i, array_values($bind));
            }

            if(!$stmt->execute())
            {
                throw new Exception($stmt->error);
            }

            return true;
        }



        public function delete(string $query, array $bind) : bool
        {
            #Prepare
            $stmt = $this->m_db->prepare($query);

            #Bind data
            for ($i = 1; $i <= count($bind); $i++)
            {
                $stmt->bindValue($i, array_values($bind));
            }

            if(!$stmt->execute())
            {
                throw new Exception($stmt->error);
            }

            return true;
        }


        public function db() : PDO
        {
            return $this->m_db;
        }

        public function lastInsertId() : ?int
        {
            return $this->m_db->lastInsertId();
        }

    }
?>