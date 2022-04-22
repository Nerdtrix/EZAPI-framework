<?php

namespace Tests;
use Models\UserAuthentication;
use Repositories\IUserAuthRepository;

class FakeUserAuthRepository implements IUserAuthRepository
{
    public $getUserByEmailCallback = null;
    public $getUserByUsernameCallback = null;

    public function getUserByEmail(string $email): UserAuthentication
    {
        return call_user_func($this->getUserByEmailCallback, $email);
    }

    public function getUserByUsername(string $username): UserAuthentication
    {
        return call_user_func($this->getUserByUsernameCallback, $username);
    }
}

?>