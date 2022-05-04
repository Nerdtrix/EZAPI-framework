<?php
    namespace Models;

    class Web2FAModel
    {
        public ?int $id;
        public ?int $userId;
        public ?int $token;
        public string $createdAt;
        public string $updatedAt;
        public ?string $deletedAt;
    }
?>