<?php
    namespace Models;
    use Attributes\Column;

    class UserAuthenticationModel
    {
        #[Column("int", 11, false, true)]
        public int $id;

        #[Column("string", 100, false, false)]
        public string $role;

        #[Column("bool", 1, true, false)]
        public bool $isTwoFactorAuth;

        #[Column("string", 10, false, false)]
        public string $status;

        #[Column("string", 100, false, false)]
        public string $fName;

        #[Column("string", 100, false, false)]
        public string $lName;

        #[Column("string", 100, false, false)]
        public string $username;

        #[Column("string", 100, false, false)]
        public string $email;  
        
        #[Column("string", 100, false, false)]
        public string $password;

        #[Column("string", 50, false, false)]
        public string $locale;
    }
?>