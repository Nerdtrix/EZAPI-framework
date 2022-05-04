<?php
    namespace Services;

    interface IWeb2FAService
    {
        function validateOTPToken(int $otp): bool;
        function sendOtpEmail(string $email) : bool;
        function createOtpSessionToken(int $userId) : bool;
    }
?>