<?php
    namespace Repositories\Auth;
    use Core\Database\Mysql\IMysql;


    interface IDevicesRepository
    {
        function getDeviceByCookieIdentifier(string $cookieIdentifier) : object;

        function addNewDevice(int $userId, string $ipAddress, string $deviceName, string $cookieIdentifier, string $expiresAt) : int;

        function deleteDeviceByToken(string $tokenIdentifier) : bool;
    }
    
    class DevicesRepository implements IDevicesRepository
    {
        private IMysql $m_db;
        private string $devicesTable = "devices";
        

        public function __construct(IMysql $mySql)
        {
            $this->m_db = $mySql;
        }

    


        /**
         * @param string cookieIdentifier
         * @return DeviceModel object
         */
        public function getDeviceByCookieIdentifier(string $cookieIdentifier) : object
        {
            $query =  "SELECT * FROM {$this->devicesTable} WHERE identifier = ? ORDER BY id DESC LIMIT 1";

            return $this->m_db->select(
                query: $query,
                bind: [$cookieIdentifier]
            );
        }


        /**
         * @param string userId
         * @param string ipAddress
         * @param string deviceName
         * @param string cookieIdentifier
         * @return int last inserted Id
         */
        public function addNewDevice(int $userId, string $ipAddress, string $deviceName, string $cookieIdentifier, string $expiresAt) : int
        {
            return $this->m_db->insert(
                query: "INSERT INTO {$this->devicesTable} SET userId = ?, ip = ?, name = ?, identifier = ?, expiresAt = ?",
                bind: [$userId, $ipAddress, $deviceName, $cookieIdentifier, $expiresAt]
            );
        }


        public function deleteDeviceByToken(string $tokenIdentifier) : bool
        {
            return $this->m_db->delete(
                query: "DELETE FROM {$this->devicesTable} WHERE identifier = ?",
                bind: [$tokenIdentifier]
            );
        }

    }