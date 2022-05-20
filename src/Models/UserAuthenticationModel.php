<?php
    namespace Models;

    class UserAuthenticationModel
    {
        public int $id;
        public string $role;
        public bool $isTwoFactorAuth;
        public string $status;
        public string $fName;
        public string $lName;
        public string $username;
        public string $email;        
        public string $password;
        public string $locale;
    }
?>