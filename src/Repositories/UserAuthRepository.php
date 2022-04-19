<?php
    namespace Repositories;
    use Exception;
    use Core\Database\Mysql\Connection;
    use Models\User;


    class UserAuthRepository implements IUserAuthRepository
    {
        protected Connection $db;
        private const COLL_NAME = "user";

        public function __construct(Connection $db)
        {
            $this->db = $db;
        }


        public function getUserByEmail(string $email): User
        {
            throw new Exception("not implemented");
        }

        public function getUserByUsername(string $username): User
        {
            throw new Exception("not implemented");
        }
    }
?>