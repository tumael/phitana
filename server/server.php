<?php

// Parse without sections
$config = parse_ini_file(__DIR__ . '/config/config.ini');

ini_set('display_errors', $config['display_errors']);
error_reporting(E_ALL);

require(__DIR__ . '/lib/SplClassLoader.php');

$classLoader = new SplClassLoader('WebSocket', __DIR__ . '/lib');
$classLoader->register();
$classLoader = new SplClassLoader('Phitana', __DIR__ . '/lib');
$classLoader->register();

$server = new \WebSocket\Server(
    $config['host'], $config['port'], $config['ssl']);

// server settings:
$server->setMaxClients($config['max_clients']);
$server->setCheckOrigin((boolean)$config['check_origin']);
//$server->setAllowedOrigin('foo.lh');
$server->setMaxConnectionsPerIp($config['max_connections']);
$server->setMaxRequestsPerMinute($config['max_request']);

foreach ($config['apps'] as $app_name) {
    $_app = '\\WebSocket\\Application\\' . ucfirst($app_name) . 'Application';
    $app = $_app::getInstance();
    $app->setServer($server);
    $server->registerApplication($app_name, $app);
}

$server->run();
