<?php
    namespace Services;

    interface IAuthenticationService
    {
        function authenticate(string $usernameOrEmail, string $password, bool $rememberMe) : object;

        function verifyOTP(string $usernameOrEmail, string $otp) : object;
    }
?>