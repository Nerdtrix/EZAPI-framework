<?php
    namespace Services;

    interface IAuthenticationService
    {
        function authenticate(string $usernameOrEmail, string $password, string $otp, bool $rememberMe) : object;
    }
?>