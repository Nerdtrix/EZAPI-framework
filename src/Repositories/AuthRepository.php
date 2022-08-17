<?php
    namespace Repositories;
    use Core\Database\Mysql\IMysql;
    use Models\{AuthModel, UserModel, StatusModel};
    use Core\Exceptions\DBError;

    interface IAuthRepository
    {
        function getUserByUsernameOrEmail(string $usernameOrEmail) : AuthModel;
        function getUserById(int $userId): UserModel;
        function updateUserStatus(int $userId, string $status) : bool;
    }

    class AuthRepository implements IAuthRepository
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
         * @param string usernameOrEmail
         * @return AuthModel
         */
        public function getUserByUsernameOrEmail(string $usernameOrEmail) : AuthModel
        {
            $getBy = filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL) ? "email" : "username";

            $query =  "SELECT user.id, user.roleId, user.statusId, user.username, user.fName, user.lName, user.password, user.email, user.locale, user.isTwoFactorAuth, role.role, status.status
                FROM {$this->userTable} user, {$this->roleTable} role, {$this->statusTable} status
                WHERE user.{$getBy} = ? AND user.statusId = status.id AND user.roleId = role.id
                ORDER BY user.{$getBy} ASC 
                LIMIT 1";

            return $this->m_db->select(
                query: $query,
                bind: [$usernameOrEmail],
                model: AuthModel::class
            );
        }


        /**
         * @param int userId
         * @return UserModel
         */
        public function getUserById(int $userId): UserModel
        {
            $query =  "SELECT user.*, role.role, status.status
                FROM {$this->userTable} user, {$this->roleTable} role, $this->statusTable status
                WHERE user.id = ? AND role.id = user.roleId AND status.id = user.statusId";

            return $this->m_db->select(
                query: $query,
                bind: [$userId],
                model: UserModel::class
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