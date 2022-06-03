<?php
    namespace Repositories;
    use Core\Database\Mysql\IMysql;
    use Models\UserModel;


    class UserRepository implements IUserRepository
    {
        private IMysql $m_db;
        private string $table = "user";
        

        public function __construct(IMysql $mySql)
        {
            $this->m_db = $mySql;
        }

        /**
         * @param int userId
         * @return UserModel
         */
        public function getById(int $userId): UserModel
        {
            return $this->m_db->select(
                query: "SELECT * FROM {$this->table} WHERE id = ? ORDER BY id ASC LIMIT 1",
                bind: [$userId],
                model: UserModel::class
            );
        }

    }
?>