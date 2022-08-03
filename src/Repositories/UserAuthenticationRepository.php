<?php
    namespace Repositories;
    use Core\Database\Mysql\IMysql;
    use Models\{UserAuthenticationModel, StatusModel};
    
    use Core\Exceptions\DBError;


    class UserAuthenticationRepository implements IUserAuthenticationRepository
    {
        private IMysql $m_db;
        private string $userTable = "user";
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
            $query =  "SELECT user.id, user.roleId, user.statusId, user.username, user.fName, user.lName, user.password, user.email, user.locale, user.isTwoFactorAuth, role.role, status.status
                FROM {$this->userTable} user, {$this->roleTable} role, {$this->statusTable} status
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
            $query =  "SELECT user.id, user.roleId, user.statusId, user.username, user.fName, user.lName, user.password, user.email, user.locale, user.isTwoFactorAuth, role.role, status.status
                FROM {$this->userTable} user, {$this->roleTable} role, {$this->statusTable} status
                WHERE user.username = ? AND user.statusId = status.id AND user.roleId = role.id
                ORDER BY user.username ASC 
                LIMIT 1";

            return $this->m_db->select(
                query: $query,
                bind: [$username],
                model: UserAuthenticationModel::class
            );
        }


        /**
         * @param int userId
         * @param string status
         * @return bool
         * @throws Exceptions
         */
        public function updateUserStatus(int $userId, string $status) : bool
        {
            $statusObj =  $this->m_db->select(
                query: "SELECT id FROM {$this->statusTable} WHERE STATUS = ?",
                bind: [$status],
                model: StatusModel::class
            );

            if(empty($statusObj->id))
            {
                throw new DBError("unknown status");
            }

            return $this->m_db->update(
                query: "UPDATE {$this->userTable} SET statusId = ? WHERE id = ?",
                bind: [$statusObj->id, $userId]
            );
        }

    }
?>