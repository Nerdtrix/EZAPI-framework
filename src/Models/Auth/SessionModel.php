<?php
    namespace Models\Auth;

    class SessionModel
    {
        public int $id;
        public int $userId;
        public int $deviceId;
        public bool $isValidated;
        public bool $isNewDevice;
        public string $token;
        public string $createdAt;
        public ?string $updatedAt;
        public string $expiresAt;      
    }
?>