<?php
    namespace Models\User;
    use Attributes\Column;

    class UserRegistrationModel
    {
        public string $role;

        public bool $isTwoFactorAuth;

        public string $status;


        public string $fName;

   
        public string $lName;


        #[Column("string", 0, 150, true, false)]
        public string $username;

        #[Column("string", 0, 150, true, false)]
        public string $email;  
        
        #[Column("string", 0, 150, true, false)]
        public string $password;
    }
?>