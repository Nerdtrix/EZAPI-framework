<?php
    namespace Repositories;
    use Models\User;

    interface IUserAuthRepository
    {
        function getUserByUsername(string $username) : ?User;
        function getUserByEmail(string $email) : ?User;
    }
?>