<?php
    namespace Services;

    interface IWeb2FAService
    {
        function createOtpMailSessionToken(object $userInfo, bool $rememberMe) : bool;
        function validateOTPMailToken(int $otp): bool;
    }
?>