<?php
  namespace Models;

  class UserModel
  {
    public ?int $id;
    public ?int $roleId;
    public ?int $statusId;
    public bool $isTwoFactorAuth;
    public string $fName;
    public string $lName;
    public string $email;
    public string $username;
    public string $locale;
    public string $createdAt;
    public string $updatedAt;
    public ?string $deletedAt;
  }
?>