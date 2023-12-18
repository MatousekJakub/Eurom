<?php


namespace Eurom\Routing;

use Eurom\Routing\RouteAttributes\RouteAttribute;
use ReflectionClass;
use Slim\Psr7\Request;
use Slim\Psr7\Response;


class BaseController
{
    function post(Request $request, Response $response, $args)
    {
        return $response->withStatus(404);
    }
    function get(Request $request, Response $response, $args)
    {
        return $response->withStatus(404);
    }
    function put(Request $request, Response $response, $args)
    {
        return $response->withStatus(404);
    }
    function delete(Request $request, Response $response, $args)
    {
        return $response->withStatus(404);
    }
    function attributeCheck(Request $request, Response $response, $args, $mth)
    {
        $reflection = new ReflectionClass($this);
        $attributes = $reflection->getAttributes();
        $methods = $reflection->getMethods();
        foreach ($attributes as $attribute) {
            $attribute = $attribute->newInstance();
            if (!$attribute instanceof RouteAttribute) continue;
            $attributeCall = $attribute->apply($request, $response, $args);
            if ($attributeCall) return $attributeCall;
        }
        foreach ($methods as $method) {
            $attributes = $method->getAttributes();
            foreach ($attributes as $attribute) {
                $attribute = $attribute->newInstance();
                if (!$attribute instanceof RouteAttribute) continue;
                $attributeCall = $attribute->apply($request, $response, $args);
                if ($attributeCall) return $attributeCall;
            }
        }
        return $this->$mth($request, $response, $args);
    }

    public function register($route, \Slim\App $app)
    {
        $app->get($route, fn ($rq, $rs, $arg) => $this->attributeCheck($rq, $rs, $arg, "get"));
        $app->post($route, fn ($rq, $rs, $arg) => $this->attributeCheck($rq, $rs, $arg, "post"));
        $app->put($route, fn ($rq, $rs, $arg) => $this->attributeCheck($rq, $rs, $arg, "put"));
        $app->delete($route, fn ($rq, $rs, $arg) => $this->attributeCheck($rq, $rs, $arg, "delete"));
    }
}
