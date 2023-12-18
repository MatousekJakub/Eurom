<?php

namespace Routes\User;

use Eurom\Routing\BaseController;
use Eurom\Routing\RouteAttributes\RequiredLogin;
use Model\UserFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

#[RequiredLogin(1)]
class Controller extends BaseController
{
    //Return all Users
    function get(Request $request, Response $response, $args)
    {
        $User = UserFactory::getAll();
        $response->getBody()->write(json_encode($User));
        return $response;
    }

    //Create a User
    function post(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        if (!$id = UserFactory::create($body)) return $response->withStatus(400);
        $User = UserFactory::getById($id);
        $response->getBody()->write(json_encode($User));
        return $response->withStatus(201);
    }
}
