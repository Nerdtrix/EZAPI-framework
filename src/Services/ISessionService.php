<?php
    namespace Services;

    interface ISessionService
    {
        function create(int $userId, bool $rememberMe = false) : bool;

        function isValid() : bool;

        function extend() : bool;

        function delete() : bool;
    }
?>