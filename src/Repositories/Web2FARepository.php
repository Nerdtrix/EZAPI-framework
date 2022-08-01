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

        public function saveOtp(int $userId, int $otp, bool $isNewDevice, string $expiresAt) : bool
        {
            return $this->m_db->insert(
                query: "INSERT INTO {$this->table} SET userId = ?, otp = ?, newDevice = ?, expiresAt = ?",
                bind: [$userId, $otp, $isNewDevice, $expiresAt]
            );
        }

        /**
         * @param int getByOtpId
         * @return Web2FAModel
         */
        public function getByOtp(int $otp): Web2FAModel
        {
            return $this->m_db->select(
                query: "SELECT * FROM {$this->table} WHERE otp = ? ORDER BY id DESC LIMIT 1",
                bind: [$otp],
                model: Web2FAModel::class
            );
        }

        /**
         * @param int token
         * @return bool
         */
        public function deleteByOtp(int $otp) : bool
        {
            return $this->m_db->delete(
                query: "DELETE FROM {$this->table} WHERE otp = ?",
                bind: [$otp]
            );
        }

        public function updateOtpByUserId(int $userId, int $otp, string $expiresAt) : bool
        {
            return $this->m_db->update(
                query: "UPDATE {$this->table} SET otp = ?, expiresAt = ? WHERE userId = ?",
                bind: [$otp, $expiresAt, $userId]
            );
        }

    }
?>