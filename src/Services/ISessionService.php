<?php
    namespace Services;

    interface ISessionService
    {
        function create(int $userId, bool $isValidated, bool $rememberMe) : bool;

        function isValid() : bool;

        function extend() : bool;

        function delete() : bool;

        function userId() : int;


        function validateOtpSession(int $userId) : bool;
    }
?>