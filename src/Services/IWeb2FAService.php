<?php
    namespace Services;

    interface IWeb2FAService
    {
        function validateOTPToken(int $otp): bool;
        function sendOtpEmail(string $name, string $email) : void;
        function createOtpSessionToken(int $userId) : bool;
    }
?>