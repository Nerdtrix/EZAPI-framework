<?php
    namespace Models\Auth;

    class DevicesModel
    {
        // #[Column(
        //     type: 'string',
        //     length: 32,
        //     unique: true,
        //     nullable: false,
        // )]
        public int $id;
        public int $userId;
        public string $ip;
        public string $name;
        public string $identifier;
        public string $createdAt;
        public string $updatedAt;
        public string $expiresAt;      
    }
?>