<?php
    namespace Repositories;
    use Core\Database\Mysql\IMysql;
    use Models\MFAModel;

    interface IMFARepository
    {
        function saveOtp(int $userId, int $otp, string $expiresAt) : bool;

        function getByOtp(int $otp): \Models\MFAModel;

        function deleteByOtp(int $otp) : bool;

       

        function updateOtpByUserId(int $userId, int $otp, string $expiresAt) : bool;
    }

    class MFARepository implements IMFARepository
    {
        private IMysql $m_db;
        private string $table = "mfa";
        

        public function __construct(IMysql $mySql)
        {
            $this->m_db = $mySql;
        }

        /**
         * @param int userId
         * @param int otp
         * @param string expiresAt
         * @return bool
         */
        public function saveOtp(int $userId, int $otp, string $expiresAt) : bool
        {
            $query = "INSERT INTO {$this->table} SET userId = ?, otp = ?, expiresAt = ?";

            return $this->m_db->insert(
                query: $query,
                bind: [
                    $userId, 
                    $otp, 
                    date($this->m_db::DATE_TIME_FORMAT, $expiresAt)
                ]
            );
        }





        /**
         * @param int getByOtpId
         * @return MFAModel
         */
        public function getByOtp(int $otp): MFAModel
        {
            return $this->m_db->select(
                query: "SELECT * FROM {$this->table} WHERE otp = ? ORDER BY id DESC LIMIT 1",
                bind: [$otp],
                model: MFAModel::class
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