<?php

namespace Routes\Login;

use Eurom\Cryptography\LoginAgent;
use Eurom\Routing\BaseController;
use Eurom\Routing\RouteAttributes\RequiredBodyParams;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Controller extends BaseController
{
    #[RequiredBodyParams("login", "pass")]
    function post(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        $user = LoginAgent::$factory->getLoginUserByLoginPass($body["login"], $body["pass"]);
        if (!$user) return $response->withStatus(401);
        $jwt = LoginAgent::$handler->getJWTToken($user);
        $response->getBody()->write(json_encode((["token" => $jwt])));
        return $response;
    }
}
