<?php

use Symfony\Component\HttpFoundation\Request;

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__ . '/../app/autoload.php';
include_once __DIR__ . '/../var/bootstrap.php.cache';

$kernel = new AppKernel('prod', false);
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
Request::setTrustedProxies(
// IPs ou plage d'IP que vous faites confiance
    ['127.0.0.1', '192.168.0.1'],
    // Constantes de confiance
    Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST
);

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
