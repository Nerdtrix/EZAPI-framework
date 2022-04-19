<?php
    namespace Repositories;
    use Exception;
    use PDO;
    use Core\Database\Mysql\{Connection, IMysql};
    use Models\User;


    class UserAuthRepository implements IUserAuthRepository
    {
        protected PDO $db;
        private const COLL_NAME = "user";

        public function __construct(Connection $db)
        {
            $this->db = $db;
        }


        public function getUserByEmail(string $email): User
        {
            $query = "SELECT * FROM ${self::COLL_NAME} WHERE email = ? LIMIT 1 ORDER BY id DESC";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(1, $email);
            $stmt->execute();
           
            $user = $stmt->fetch(PDO::FETCH_CLASS, User::class);

            return $user;
        }

        public function getUserByUsername(string $username): User
        {
            throw new Exception("not implemented");
        }
    }
?>