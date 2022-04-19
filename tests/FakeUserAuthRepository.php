<?php

namespace Tests;
use Models\User;
use Repositories\IUserAuthRepository;

class FakeUserAuthRepository implements IUserAuthRepository
{
    public $getUserByEmailCallback = null;
    public $getUserByUsernameCallback = null;

    public function getUserByEmail(string $email): ?User
    {
        return call_user_func($this->getUserByEmailCallback, $email);
    }

    public function getUserByUsername(string $username): ?User
    {
        return call_user_func($this->getUserByUsernameCallback, $username);
    }
}

?>