<?php
    namespace Repositories;
    use Models\User;

    interface IUserRepository
    {
        function getById(int $userId): User;
    }
?>