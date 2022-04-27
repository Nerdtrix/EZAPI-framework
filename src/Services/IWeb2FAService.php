<?php
    namespace Services;

    interface IWeb2FAService
    {
        function validateOTPToken(string $otp): bool;
        function sendOtpEmail(string $email) : bool;
        function sendNewDeviceDetected(string $email) : void;
    }
?>