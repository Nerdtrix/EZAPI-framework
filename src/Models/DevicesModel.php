<?php
    namespace Models;

    class DevicesModel
    {
        public int $id;
        public int $userId;
        public string $ipAddress;
        public string $deviceName;
        public string $cookieIdentifier;
        public string $createdAt;
        public string $updatedAt;
        public string $expiresAt;
        public string $expire;        
    }
?>