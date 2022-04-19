<?php
    namespace Core\Database\Mysql;
    use \PDO;

    class Mysql implements IMysql
    {
        protected PDO $m_connection;

        public function __construct(Connection $connection) 
        {
            $this->m_connection = $connection;
        }

        public function select(){}

        public function where(){}

        public function bind(){}

        public function limit(){}

        public function offset(){}

        public function order(){}

        public function query(){}

        public function execute(){}

        public function assign()
        {

        }

    }
?>