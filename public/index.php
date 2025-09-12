<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Router.php';

ray()->clearAll();

$router = new Router();

$router->get('/api/user/:user_id/:location', function ($req, $res) {
    ray($req->params);
    $res::sendResponse();
});

$router->get('/api/user/:user_id', function ($req, $res) {
    ray($req->params);
    $res::sendResponse();
});

$router->dispatch();
