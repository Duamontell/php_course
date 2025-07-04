<?php

require_once __DIR__ . "/../store/UserTable.php";

class UserController
{
    private UserTable $userTable;

    public function __construct(PDO $pdo)
    {
        $this->userTable = new UserTable($pdo);
    }

    public function index() {}

    public function getUserTable(): UserTable
    {
        return $this->userTable;
    }
    // public function registrationUser() {}
}
