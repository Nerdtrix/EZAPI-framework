<?php
    namespace Core\Database\Mysql;
    use \PDO;

    class Mysql
    {
        protected PDO $m_connection;

        public function __construct(Connection $connection) 
        {
            $this->m_connection = $connection->connect();
        }

        public function select(){}

        public function where(){}


        public function bind(){}


        public function limit(){}

        public function offset(){}

        public function order(){}

        public function query(){}

    }
?>