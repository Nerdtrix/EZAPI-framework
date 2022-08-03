<?php
    namespace Repositories;

    interface IUserRepository
    {
        function getById(int $userId): \Models\UserModel;

    }
?>