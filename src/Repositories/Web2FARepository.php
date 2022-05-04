<?php
    namespace Repositories;
    use Core\Database\Mysql\IMysql;
    use Models\Web2FAModel;


    class Web2FARepository implements IWeb2FARepository
    {
        private IMysql $m_db;
        private string $table = "otp";
        

        public function __construct(IMysql $mySql)
        {
            $this->m_db = $mySql;
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

    }