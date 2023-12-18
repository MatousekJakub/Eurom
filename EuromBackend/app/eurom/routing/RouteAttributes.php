<?php

namespace Eurom\Routing\RouteAttributes;

use Attribute;
use Eurom\Cryptography\LoginAgent;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

#[Attribute]
abstract class RouteAttribute
{
    abstract function apply(Request $request, Response $response, $args): false | Response;
}

#[Attribute]
class RequiredLogin extends RouteAttribute
{
    function __construct(public int $adminLevel = 0)
    {
    }
    function apply(Request $request, Response $response, $args): false | Response
    {
        $loginResult = LoginAgent::$handler->requireLogin($request, $response, $this->adminLevel);
        if (!$loginResult || $loginResult === 200)
            return false;
        return $response->withStatus($loginResult);
    }
}

#[Attribute]
class RequiredArgs extends RouteAttribute
{
    public $args = [];
    function __construct(string ...$args)
    {
        foreach ($args as $value) {
            $this->args[] = $value;
        }
    }
    function apply(Request $request, Response $response, $args): false | Response
    {
        foreach ($this->args as $arg) {
            if (!array_key_exists($arg, $args))
                return $response->withStatus(400);
        }
        return false;
    }
}

#[Attribute]
class RequiredBodyParams extends RouteAttribute
{
    public $params = [];
    function __construct(string ...$params)
    {
        foreach ($params as $value) {
            $this->params[] = $value;
        }
    }
    function apply(Request $request, Response $response, $args): false | Response
    {
        foreach ($this->params as $param) {
            $body = $request->getParsedBody() ?? [];
            if (!array_key_exists($param, $body))
                return $response->withStatus(400);
        }
        return false;
    }
}
