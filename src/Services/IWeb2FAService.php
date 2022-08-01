<?php
    namespace Services;

    interface IWeb2FAService
    {
        function createOtpMailSessionToken(object $userInfo, bool $rememberMe, bool $isNewDevice) : bool;
        function validateOTP(int $otp): \Models\Web2FAModel;
        function resendOTPMail() : bool;
    }
?>