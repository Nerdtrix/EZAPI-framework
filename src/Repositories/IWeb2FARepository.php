<?php
    namespace Repositories;

    interface IWeb2FARepository
    {
        function getByOtpId(int $token): \Models\Web2FAModel;

        function deleteByOtpId(int $token) : bool;

        function saveOtp(int $userId, int $otp, string $expiresAt) : bool;
    }
?>