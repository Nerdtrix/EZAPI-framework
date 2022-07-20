<?php
    namespace Repositories;
    use Core\Database\Mysql\IMysql;
    use Models\Web2FAModel;


    class Web2FARepository implements IWeb2FARepository
    {
        private IMysql $m_db;
        private string $table = "web2fa";
        

        public function __construct(IMysql $mySql)
        {
            $this->m_db = $mySql;
        }

        public function saveOtp(int $userId, int $otp, string $expiresAt) : bool
        {
            return $this->m_db->insert(
                query: "INSERT INTO {$this->table} SET userId = ?, otp = ?, expiresAt = ?",
                bind: [$userId, $otp, $expiresAt]
            );
        }

        /**
         * @param int getByOtpId
         * @return Web2FAModel
         */
        public function getByOtpId(int $token): Web2FAModel
        {
            return $this->m_db->select(
                query: "SELECT * FROM {$this->table} WHERE token = ? LIMIT 1",
                bind: [$token],
                model: Web2FAModel::class
            );
        }

        /**
         * @param int token
         * @return bool
         */
        public function deleteByOtpId(int $token) : bool
        {
            return $this->m_db->delete(
                query: "DELETE FROM {$this->table} WHERE token = ?",
                bind: [$token]
            );
        }

    }
?>