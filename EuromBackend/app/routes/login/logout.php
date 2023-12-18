<?php

namespace Routes\Login\Logout;

use Eurom\Routing\BaseController;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Controller extends BaseController
{

    function get(Request $request, Response $response, $args)
    {
        $response->getBody()->write(json_encode("Logout"));
        return $response;
    }
}
