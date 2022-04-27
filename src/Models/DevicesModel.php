<?php
    namespace Models;

    class DevicesModel
    {
        public int $id;
        public int $userId;
        public string $ip;
        public string $name;
        public string $createdAt;
        public ?string $updatedAt;        
        public ?string $deletedAt;
    }
?>