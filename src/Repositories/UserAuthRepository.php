<?php
    namespace Repositories;
    use Core\Database\Mysql\{IMysql, Mysql};
    use Models\UserAuthentication;


    class UserAuthRepository implements IUserAuthRepository
    {
        private Mysql $m_db;
        private string $table = "user";
        

        public function __construct(IMysql $mySql)
        {
            $this->m_db = $mySql;
        }


        /**
         * @param string email
         * @return UserAuthentication
         */
        public function getUserByEmail(string $email): UserAuthentication
        {
            $query = "SELECT * FROM {$this->table} WHERE email = ? ORDER BY email DESC LIMIT 1"; 

            return $this->m_db->execute(
                query: $query,
                bind: [$email],
                model: UserAuthentication::class
            );
        }


        /**
         * @param string username
         * @return UserAuthentication
         */
        public function getUserByUsername(string $username): UserAuthentication
        {
            $query = "SELECT * FROM {$this->table} WHERE username = ? ORDER BY username DESC LIMIT 1"; 

            return $this->m_db->execute(
                query: $query,
                bind: [$username],
                model: UserAuthentication::class
            );
        }
    }
?>