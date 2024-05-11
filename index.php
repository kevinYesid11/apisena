<?php

require 'vendor/autoload.php';
require_once 'auth.php';

use FastRoute\RouteCollector;
use FastRoute\Dispatcher;

$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute('POST', '/register', 'registerHandler');
    $r->addRoute('POST', '/login', 'loginHandler');
});

function registerHandler()
{
    $requestBody = json_decode(file_get_contents('php://input'), true);
    $username = $requestBody['username'];
    $password = $requestBody['password'];

    return registerUser($username, $password);
}

function loginHandler()
{
    $requestBody = json_decode(file_get_contents('php://input'), true);
    $username = $requestBody['username'];
    $password = $requestBody['password'];

    if (authenticate($username, $password)) {
        return json_encode(["message" => "Autenticacion satisfactoria"]);
    } else {
        header('HTTP/1.1 401 Unauthorized');
        return json_encode(["error" => "Error en la autenticacion"]);
    }
}

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Remove query string and trim trailing slash
$uri = explode('?', $uri)[0];
$uri = rtrim($uri, '/');

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        header('HTTP/1.1 404 Not Found');
        echo json_encode(["error" => '404 - Not Found']);
        break;
    case Dispatcher::METHOD_NOT_ALLOWED:
        header('HTTP/1.1 405 Method Not Allowed');
        echo json_encode(["error" => '405 - Method Not Allowed']);
        break;
    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        echo $handler($vars);
        break;
}
