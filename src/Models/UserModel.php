<?php
  namespace Models;

  class UserModel
  {
    public string $role;
    public string $status;
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