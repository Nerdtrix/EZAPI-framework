<?php
    namespace Models;

    class AuthModel
    {
        public ?int $id;
        public string $role;
        public string $status;
        public string $fName;
        public string $lName;
        public string $password;
        public bool $isTwoFactorAuth;
        public string $email;
        public string $username;
        public string $locale;
    }
?>