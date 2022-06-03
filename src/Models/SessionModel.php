<?php
    namespace Models;

    class SessionModel
    {
        public int $id;
        public int $userId;
        public int $deviceId;
        public bool $isValidated;
        public string $token;
        public string $createdAt;
        public string $updatedAt;
        public string $expiresAt;      
    }
?>