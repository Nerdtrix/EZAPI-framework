<?php
    namespace Models\Auth;

    class MFAModel
    {
        public ?int $id;
        public ?int $userId;
        public ?int $otp;
        public string $expiresAt;
    }
?>