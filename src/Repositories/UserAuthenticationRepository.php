<?php
    namespace Repositories;
    use Core\Database\Mysql\IMysql;
    use Models\{UserAuthenticationModel};


    class UserAuthenticationRepository implements IUserAuthenticationRepository
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
         * @param string email
         * @return UserAuthenticationModel
         */
        public function getUserByEmail(string $email) : UserAuthenticationModel
        {
            $query =  "SELECT user.id, user.roleId, user.statusId, user.username, user.fName, user.lName, user.password, user.email, user.isTwoFactorAuth, role.role, status.status
                FROM {$this->usertable} user, {$this->roleTable} role, {$this->statusTable} status
                WHERE user.email = ? AND user.statusId = status.id AND user.roleId = role.id
                ORDER BY user.email ASC 
                LIMIT 1";

            return $this->m_db->select(
                query: $query,
                bind: [$email],
                model: UserAuthenticationModel::class
            );
        }


        /**
         * @param string username
         * @return UserAuthenticationModel
         */
        public function getUserByUsername(string $username) : UserAuthenticationModel
        {            
            $query =  "SELECT user.id, user.roleId, user.statusId, user.username, user.fName, user.lName, user.password, user.email, user.isTwoFactorAuth, role.role, status.status
                FROM {$this->usertable} user, {$this->roleTable} role, {$this->statusTable} status
                WHERE user.username = ? AND user.statusId = status.id AND user.roleId = role.id
                ORDER BY user.username ASC 
                LIMIT 1";

            return $this->m_db->select(
                query: $query,
                bind: [$username],
                model: UserAuthenticationModel::class
            );
        }
    }
?>