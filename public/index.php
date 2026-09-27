<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$app = \App\Core\Application::create(__DIR__ . '/..');
$app->boot();
$app->handle();