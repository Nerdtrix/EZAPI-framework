<?php
    namespace Repositories;
    use Core\Database\Mysql\IMysql;
    use Models\UserModel;


    class UserRepository implements IUserRepository
    {
        private IMysql $m_db;
        private string $usertable = "user";
        private string $roleTable = "role";
        private string $statusTable = "status";
        

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
            $query =  "SELECT user.*, role.role, status.status
                FROM {$this->usertable} user, {$this->roleTable} role, $this->statusTable status
                WHERE user.id = ? AND role.id = user.roleId AND status.id = user.statusId";

            return $this->m_db->select(
                query: $query,
                bind: [$userId],
                model: UserModel::class
            );
        }

        

    }
?>