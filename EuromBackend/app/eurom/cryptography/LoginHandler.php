<?php

namespace Eurom\Cryptography;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class LoginHandler
{
    function __construct(private string $privateKeyPath)
    {
    }
    function getJWTToken(LoginUser $user): string
    {
        $privateKey = file_get_contents(str_replace("index.php", "", $_SERVER['SCRIPT_FILENAME']) . $this->privateKeyPath);
        $time = time();
        $token = [
            "iss" => "dilynatryskac.cz",
            "aud" => "dilynatryskac.cz",
            "iat" => $time,
            "nbf" => $time,
            "exp" => time() + 60 * 60 * 24 * 7,
            "data" => ["login" => $user->login, "id" => $user->id, "adminLevel" => $user->adminLevel]
        ];
        return JWT::encode($token, $privateKey, 'HS512');
    }
    function verifyJWTToken(string $token): object | false
    {
        $privateKey = file_get_contents(str_replace("index.php", "", $_SERVER['SCRIPT_FILENAME']) . $this->privateKeyPath);
        if (!preg_match('/Bearer\s(\S+)/', $token, $matches))
            return false;
        try {
            $jwt = JWT::decode(str_replace("Bearer ", "", $token), new Key($privateKey, 'HS512'));
        } catch (Exception $e) {
            return false;
        }
        return $jwt;
    }
    function requireLogin(Request $request, Response $response, bool | int $admin = 0): false | int
    {
        $bearer = $request->getHeaderLine("Authorization");
        $verify = self::verifyJWTToken($bearer);
        if ($verify === false)
            return 401;
        if ($admin !== 0 && $verify->data->adminLevel > $admin)
            return 403;
        LoginAgent::$user = new LoginUser($verify->data->id, $verify->data->adminLevel, $verify->data->login);
        return 200;
    }
}
