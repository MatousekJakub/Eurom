<?php

namespace Model\Auth;

use Eurom\Cryptography\LoginUser;
use Eurom\Cryptography\LoginUserFactory as CryptographyLoginUserFactory;

class LoginUserFactory extends CryptographyLoginUserFactory
{
    function getLoginUserById(int $id): LoginUser | false
    {
        /* if (!$user = UserFactory::getById($id)) return false;
        return new LoginUser($user->id, $user->adminLevel, $user->name); */
        return new LoginUser(1, 1, "admin");
    }
    function getLoginUserByLoginPass(string $login, string $pass): LoginUser | false
    {
        /* $user = UserFactory::getAllWhere(["name" => $login, "pass" => $pass]);
        if (count($user) === 0) return false;
        $user = $user[0];
        return new LoginUser($user->id, $user->adminLevel, $user->name); */
        return new LoginUser(1, 1, "admin");
    }
}
