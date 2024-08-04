<?php

use App\Http\Request;
use App\Service\Router;
use App\Service\ServiceContainer;

$container = require __DIR__ . '/../src/config.php';

$request = Request::fromEnv();
/** @var ServiceContainer $container */
$container->set(Request::class, $request);

$container
    ->get(Router::class)
    ->dispatch($request)
    ->send();


