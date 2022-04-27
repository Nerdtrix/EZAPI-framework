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
         * @param int limit (obtional 30 by default)
         * @param int offset (obtional 0 by default)
         * @param string orderBy (optional last record by id by default)
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
         * @param string cookieIdentifier
         * @return DeviceModel object
         */
        public function getDeviceByCookieIdentifier(string $cookieIdentifier) : DevicesModel
        {
            $query =  "SELECT * FROM {$this->devicesTable} WHERE identifier = ? ORDER BY id DESC LIMIT 1";

            return $this->m_db->select(
                query: $query,
                bind: [$cookieIdentifier],
                model: DevicesModel::class
            );
        }




        /**
         * @param string userId
         * @param string ipAddress
         * @param string deviceName
         * @param string cookieIdentifier
         * @return int last inserted Id
         */
        public function addNewDevice(int $userId, string $ipAddress, string $deviceName, string $cookieIdentifier) : int
        {
            $query =  "INSERT INTO {$this->devicesTable} SET userId = ?, ip = ?, name = ?, identifier = ?";

            return $this->m_db->insert(
                query: $query,
                bind: [$userId, $ipAddress, $deviceName, $cookieIdentifier]
            );
        }

    }