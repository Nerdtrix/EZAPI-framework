<?php
    namespace Services;

    interface IAuthenticationService
    {
        public function authenticate(string $usernameOrEmail, string $password, bool $rememberMe) : object;
    }
?>