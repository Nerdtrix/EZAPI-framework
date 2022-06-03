<?php
    namespace Services;

    interface IWeb2FAService
    {
        function createOtpMailSessionToken(int $userId) : bool;
        function validateOTPMailToken(int $otp): bool;
    }
?>