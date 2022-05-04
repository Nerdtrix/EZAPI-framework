<?php
    namespace Repositories;

    interface IWeb2FARepository
    {
        function getByOtpId(int $token): \Models\Web2FAModel;
    }
?>