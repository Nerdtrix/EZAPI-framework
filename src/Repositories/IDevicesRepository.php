<?php
    namespace Repositories;

    interface IDevicesRepository
    {
        function getDevicesByUserId(int $userId, int $limit = 30, int $offset = 0, string $orderBy = "id DESC") : \stdClass;

        function addNewDevice(int $userId, string $ipAddress, string $deviceName) : int;
    }
?>