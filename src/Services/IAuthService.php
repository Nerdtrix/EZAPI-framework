<?php
    namespace Services;

    interface IAuthService
    {
        function authenticate(string $usernameOrEmail, string $password, bool $rememberMe) : object;
        function verifyOTP(int $otp) : object;

        function endSession() : bool;

        function resendOTP() : bool;

        function isLogged() : bool;
    }
?>