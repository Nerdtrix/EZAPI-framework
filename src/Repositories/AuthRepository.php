<?php
    namespace Repositories;
    use Core\Database\Mysql\IMysql;
    use Models\{AuthModel, UserModel, StatusModel};
    use Core\Exceptions\DBError;
use Exception;

    interface IAuthRepository
    {
        function addNewUser(object $user) : int;
        function getUserByUsernameOrEmail(string $usernameOrEmail) : AuthModel;
        function getUserById(int $userId): UserModel;
        function updateUserStatus(int $userId, string $status) : bool;
        function updatePasswordByUserId(int $userId, string $password) : bool;
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
         * @param object user
         * @return int userId
         */
        public function addNewUser(object $user) : int
        {
            $status =  $this->m_db->select(
                query: "SELECT id FROM {$this->statusTable} WHERE STATUS = ?",
                bind: [$user->status]
            );

            if(empty($status->id))
            {
                throw new Exception("invalid user status");
            }

            $role =  $this->m_db->select(
                query: "SELECT id FROM {$this->roleTable} WHERE ROLE = ?",
                bind: [$user->role]
            );

            if(empty($role->id))
            {
                throw new Exception("invalid user role");
            }

            //save all
            $insertQuery = "INSERT INTO {$this->userTable} SET 
                fName = ?, 
                lName = ?, 
                email = ?, 
                username = ?, 
                locale = ?,
                roleId = ?,
                statusId = ?,
                ";


            return $this->m_db->insert(
                query: $insertQuery,
                bind: [
                    $user->fName,
                    $user->lName,
                    $user->email,
                    $user->username,
                    $user->locale,
                    $status->id,
                    $role->id
                ]
            );
        }

        /**
         * @param int userId
         * @param string password
         * @return bool
         */
        public function updatePasswordByUserId(int $userId, string $password) : bool
        {
            return $this->m_db->update(
                query: "UPDATE {$this->userTable} SET password = ? WHERE id = ?",
                bind: [$password, $userId]
            );
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