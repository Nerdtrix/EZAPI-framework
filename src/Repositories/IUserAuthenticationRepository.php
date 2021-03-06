<?php
    namespace Repositories;

    interface IUserAuthenticationRepository
    {
        function getUserByUsername(string $username) : \Models\UserAuthenticationModel;
        function getUserByEmail(string $email) : \Models\UserAuthenticationModel;
    }
?>