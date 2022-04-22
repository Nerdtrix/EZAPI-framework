<?php
    namespace Repositories;
    use Models\UserAuthentication;

    interface IUserAuthRepository
    {
        function getUserByUsername(string $username) : UserAuthentication;
        function getUserByEmail(string $email) : UserAuthentication;
    }
?>