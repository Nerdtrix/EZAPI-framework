<?php
    namespace Repositories;
    use Core\Database\Mysql\IMysql;
    use Models\SessionModel;
    use stdClass;

    interface ISessionRepository
    {
        function create(int $userId, int $deviceId, string $token, bool $isNewDevice, bool $isValidated, string $expiresAt) : int;

        function listByUserId(int $userId) : object;

        function getBySessionToken(string $token): \Models\SessionModel;

        function getByUserId(int $userId, int $deviceId): \Models\SessionModel;

        function deleteById(int $sessionId): bool;

        function extendExpirationTime(int $time, string $sessionToken) : bool;

        function deleteByToken(string $token): bool;

        function updateValidation(int $userId, int $sessionId, bool $isValidated) : bool;

        function updateIsNewDevice(int $deviceId, bool $isNewDevice) : bool;

        function getById(int $sessionId): SessionModel;

        
    }

    class SessionRepository implements ISessionRepository
    {
        private IMysql $m_db;
        private string $table = "session";
        

        public function __construct(IMysql $mySql)
        {
            $this->m_db = $mySql;
        }

        
        public function create(
            int $userId, 
            int $deviceId, 
            string $token, 
            bool $isNewDevice, 
            bool $isValidated, 
            string $expiresAt) : int
        {
            $this->m_db->insert(
                query: "INSERT INTO {$this->table} SET userId = ?, deviceId = ?, token = ?, expiresAt = ?, isValidated = ?, isNewDevice = ?",
                bind: [$userId, $deviceId, $token, date($this->m_db::DATE_TIME_FORMAT, $expiresAt), $isValidated, $isNewDevice]
            );

            return $this->m_db->lastInsertId();
        }


        public function listByUserId(int $userId) : object
        {
            return $this->m_db->select(
                query: "SELECT * FROM {$this->table} WHERE userId = ?",
                bind: [$userId],
                model: SessionModel::class
            );
        }


         /**
         * @param int userId
         * @param int deviceId
         * @return SessionModel
         */
        public function getByUserId(int $userId, int $deviceId): SessionModel
        {
            return $this->m_db->select(
                query: "SELECT * FROM {$this->table} WHERE userId = ? AND deviceId = ? ORDER BY id DESC LIMIT 1",
                bind: [$userId, $deviceId],
                model: SessionModel::class
            );
        }


        /**
         * @param int userId
         * @param int deviceId
         * @return SessionModel
         */
        public function getById(int $sessionId): SessionModel
        {
            return $this->m_db->select(
                query: "SELECT * FROM {$this->table} WHERE id = ? ORDER BY id DESC LIMIT 1",
                bind: [$sessionId],
                model: SessionModel::class
            );
        }


         /**
         * @param string name
         * @return SessionModel
         */
        public function getBySessionToken(string $token): SessionModel
        {
            return $this->m_db->select(
                query: "SELECT * FROM {$this->table} WHERE token = ? LIMIT 1",
                bind: [$token],
                model: SessionModel::class
            );
        }


        public function deleteById(int $sessionId): bool 
        {
            return $this->m_db->delete(
                query: "DELETE FROM {$this->table} WHERE id = ?",
                bind: [$sessionId]
            );
        }

        public function deleteByToken(string $token): bool 
        {
            return $this->m_db->delete(
                query: "DELETE FROM {$this->table} WHERE token = ?",
                bind: [$token]
            );
        }


        public function extendExpirationTime(int $time, string $sessionToken) : bool
        {
            return $this->m_db->update(
                query: "UPDATE {$this->table} SET expiresAt = ? WHERE token = ?",
                bind: [date($this->m_db::DATE_TIME_FORMAT, $time), $sessionToken]
            );
        }


        public function updateValidation(int $userId, int $sessionId, bool $isValidated) : bool
        {
            return $this->m_db->update(
                query: "UPDATE {$this->table} SET isValidated = ? WHERE id = ? AND userId = ?",
                bind: [$isValidated, $sessionId, $userId]
            );
        }

        /**
         * @param int sessionId
         * @param bool isNewDevice
         * @return bool
         */
        public function updateIsNewDevice(int $sessionId, bool $isNewDevice) : bool
        {
            return $this->m_db->update(
                query: "UPDATE {$this->table} SET isNewDevice = ? WHERE id = ?",
                bind: [$isNewDevice, $sessionId]
            );
        }
    }
?>