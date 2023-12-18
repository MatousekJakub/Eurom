<?php

namespace Routes\Login\Check;

use Eurom\Cryptography\LoginAgent;
use Eurom\Cryptography\LoginHandler;
use Eurom\Routing\BaseController;
use Eurom\Routing\RouteAttributes\RequiredLogin;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Controller extends BaseController
{
    #[RequiredLogin(1)]
    function get(Request $request, Response $response, $args)
    {
        $response->getBody()->write(json_encode(LoginAgent::$user));
        return $response;
    }
}
