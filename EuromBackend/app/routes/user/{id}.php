<?php

namespace Routes\User\Id;

use Eurom\Routing\BaseController;
use Eurom\Routing\RouteAttributes\RequiredArgs;
use Eurom\Routing\RouteAttributes\RequiredLogin;
use Model\UserFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

#[RequiredArgs("id"), RequiredLogin(1)]
class Controller extends BaseController
{
    //Return a User with id
    function get(Request $request, Response $response, $args)
    {
        if (!UserFactory::exists($args["id"])) return $response->withStatus(404);
        $User = UserFactory::getById($args["id"]);
        $response->getBody()->write(json_encode($User));
        return $response;
    }
    //Edit a User with id
    function post(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        $id = $args["id"];
        if (!UserFactory::exists($id)) return $response->withStatus(404);
        $User = UserFactory::getById($id);
        if (!$User->setAll($body))
            return $response->withStatus(400);
        $response->getBody()->write(json_encode($User));
        return $response->withStatus(200);
    }
    function delete(Request $request, Response $response, $args)
    {
        $id = $args["id"];
        if (!UserFactory::exists($id)) return $response->withStatus(404);
        $User = UserFactory::getById($id);
        if (!$User->delete())
            return $response->withStatus(400);
        return $response->withStatus(204);
    }
}
