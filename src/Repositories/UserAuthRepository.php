<?php
    namespace Repositories;
    use Core\Database\Mysql\IDatabase;
    use Models\User;


    class UserAuthRepository implements IUserAuthRepository
    {
        protected IDatabase $db;
        private const COLL_NAME = "user";

        public function __construct(IDatabase $db)
        {
            $this->db = $db;
        }

        //example 3
        function findFirst2(string $identifier, string $key) : User
        {
            $query = "SELECT * FROM {self::COLL_NAME} WHERE x = ?";
            $this->db->query($query);

            return new user;
        }
       

        //example 2
        function findFirst1(string $identifier, string $key) : User
        {
            $this->db->select()->from(self::COLL_NAME)->where("%s= ?")->or()->bind()->limit(1)->offset(0)->order("DESC");

            return new user;
        }

        //example 1
        function findFirst(string $identifier, string $key) : User
        {
            
            
            $this->db->select([
                "where" => sprintf("%s= ?", key($array)),
                "bind" => [current($array)]
            ], self::COLL_NAME);

            return new user;
        }

        /**
         * @method findFIrst
         * @param array  ex: ["id" => "value"]
         * @return object
         */
        public function findFirsts(array $identifier, string $key) : User
        {
           return new user;
        }
    }



// UserRepository
// Irepository<BaseEntity>
// find(long id) : User
// insert(T entity) : User //generig interface
// update(T entity) : void
// Delete(T entity) : void
// save() : void
