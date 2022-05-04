<?php
    namespace Repositories;
    use Core\Database\Mysql\IMysql;
    use Models\SessionModel;
use stdClass;

    class SessionRepository implements ISessionRepository
    {
        private IMysql $m_db;
        private string $table = "session";
        

        public function __construct(IMysql $mySql)
        {
            $this->m_db = $mySql;
        }

        public function create(int $userId, string $token, int $expiresAt) : bool
        {
            return $this->m_db->insert(
                query: "INSERT INTO {$this->table} SET userId = ? token = ? expiresAt = ?",
                bind: [$userId, $token, $expiresAt]
            );
        }

        public function listByUserId(int $userId) : stdClass
        {
            return $this->m_db->select(
                query: "SELECT * FROM {$this->table} WHERE userId = ?",
                bind: [$userId],
                model: SessionModel::class
            );
        }

         /**
         * @param int userId
         * @return SessionModel
         */
        public function getByUserId(int $userId): SessionModel
        {
            return $this->m_db->select(
                query: "SELECT * FROM {$this->table} WHERE userId = ? LIMIT 1",
                bind: [$userId],
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
                bind: [$time, $sessionToken]
            );
        }

        public function getDateTimeFormat() : string
        {
            return $this->m_db::DATE_TIME_FORMAT;
        }

    }