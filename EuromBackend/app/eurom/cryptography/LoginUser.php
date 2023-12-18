<?php

namespace Eurom\Cryptography;

class LoginUser
{
    function __construct(public int $id, public int $adminLevel, public string $login)
    {
    }
}
abstract class LoginUserFactory
{
    abstract function getLoginUserById(int $id): LoginUser | false;
    abstract function getLoginUserByLoginPass(string $login, string $pass): LoginUser | false;
}
