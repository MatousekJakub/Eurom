<?php

use Eurom\Cryptography\LoginAgent;
use Eurom\Cryptography\LoginHandler;
use Eurom\Routing\Router;
use Model\Auth\LoginUserFactory;
use Slim\Factory\AppFactory;


LoginAgent::$handler = new LoginHandler("key.pem");
LoginAgent::$factory = new LoginUserFactory();

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$router = new Router(__DIR__ . "/routes");
$router->applyRoutes($app);

$app->run();
