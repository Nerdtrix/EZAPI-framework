<?php
    namespace Services;

    interface IDevicesService
    {
        function listDevicesByUserId(string $userId) : \stdClass;
        function isNewDevice() : bool;
        function addNewDevice(int $userId) : void;
        function sendNewDeviceDetectedEmail(string $name, string $email) : void;

        function getDeviceId() : int;
    }
?>