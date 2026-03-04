<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload.php';

$env = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? 'prod';
$debug = $_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? false;

$kernel = new Kernel($env, (bool) $debug);
$request = Symfony\Component\HttpFoundation\Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
