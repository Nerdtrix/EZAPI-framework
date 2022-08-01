<?php
    namespace Repositories;

    interface IWeb2FARepository
    {
        function getByOtp(int $otp): \Models\Web2FAModel;

        function deleteByOtp(int $otp) : bool;

        function saveOtp(int $userId, int $otp, bool $isNewDevice, string $expiresAt) : bool;

        function updateOtpByUserId(int $userId, int $otp, string $expiresAt) : bool;
    }
?>