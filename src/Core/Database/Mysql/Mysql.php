<?php
    namespace Core\Database\Mysql;
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
         * @return object
         */
        public function execute(string $query, array $bind, string $model) : object
        {
            $stmt = $this->m_db->prepare($query);

            $offset = 1;
            foreach($bind as $value) 
            {
                $stmt->bindValue($offset, $value);
                $offset++;
            }

            //$stmt->setFetchMode(PDO::FETCH_CLASS, $model); 

            $stmt->execute();

            $results = $stmt->fetch(\PDO::FETCH_ASSOC);

            $model = new $model();
            foreach ($results as $key => $value) 
            {
                if (property_exists($model, $key)) 
                {
                    $model->$key = $value;
                }
            }
           
            return $model;
        }

        public function db() : PDO
        {
            return $this->m_db;
        }





        // public function select(string $select = "*")
        // {
        //     $this->query = "SELECT ${select}";
        // }


        // public function from(string $table)
        // {
        //     $this->query .= "FROM ${table}";
        // }

        // public function get(string $table)
        // {

        // }


        // public function where(string $where)
        // {
        //     $this->query .= "WHERE ${where}";
        // }

        

        // public function bind(array $bind)
        // {
        //     $offset = 1;
        //     foreach($bind as $value) 
        //     {
        //         //$stmt->bindValue($bindCount, $value);

        //         $offset++;
        //     }
        // }

        // public function create(){}

        // public function update(){}

        // public function distroy(){}

        // public function join(){}

        // public function limit(){}

        // public function offset(){}

        // public function orderBy(string $column, string $direction = "asc")
        // {
            
        // }

        // public function query(){}


        // public function assign()
        // {

        // }

    }
?>