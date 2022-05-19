<?php
    namespace Repositories;

    interface IDevicesRepository
    {
        function getDevicesByUserId(int $userId, int $limit = 30, int $offset = 0, string $orderBy = "id DESC") : \stdClass;

        function getDeviceByCookieIdentifier(string $cookieIdentifier) : \Models\DevicesModel;

        function addNewDevice(int $userId, string $ipAddress, string $deviceName, string $cookieIdentifier, string $expiresAt) : int;
    }
?>