<?php
    namespace Models;

    class Web2FAModel
    {
        public ?int $id;
        public ?int $userId;
        public ?int $otp;
        public bool $newDevice;
        public string $expiresAt;
    }
?>