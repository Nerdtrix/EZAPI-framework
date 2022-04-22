<?php
    namespace Repositories;
    use Core\Database\Mysql\{IMysql, Mysql};
    use Models\User;


    class UserRepository implements IUserRepository
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
        public function getById(int $userId): User
        {
            $query = "SELECT * FROM {$this->table} WHERE id = ? ORDER BY id DESC LIMIT 1"; 

            return $this->m_db->execute(
                query: $query,
                bind: [$userId],
                model: UserAuthentication::class
            );
        }

    }