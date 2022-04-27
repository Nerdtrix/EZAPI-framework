<?php
    namespace Repositories;
    use Core\Database\Mysql\IMysql;
    use Models\DevicesModel;


    class DevicesRepository implements IDevicesRepository
    {
        private IMysql $m_db;
        private string $devicesTable = "devices";
        

        public function __construct(IMysql $mySql)
        {
            $this->m_db = $mySql;
        }

        /**
         * @param int userId
         * @return stdClass object of models
         */
        public function getDevicesByUserId(int $userId, int $limit = 30, int $offset = 0, string $orderBy = "id DESC") : \stdClass
        {
            $query =  "SELECT * FROM {$this->devicesTable} WHERE userId = ? ORDER BY {$orderBy} LIMIT {$limit} OFFSET $offset";

            return $this->m_db->select(
                query: $query,
                bind: [$userId],
                model: DevicesModel::class
            );
        }

        /**
         * @param string userId
         * @param string ipAddress
         * @param string deviceName
         * @return int last inserted Id
         */
        public function addNewDevice(int $userId, string $ipAddress, string $deviceName) : int
        {
            $query =  "INSERT INTO {$this->devicesTable} SET userId = ?, ip = ?, name = ?";

            return $this->m_db->insert(
                query: $query,
                bind: [$userId, $ipAddress, $deviceName]
            );
        }

        public function updateDevice(){}

        public function deleteDevice(){}

    }