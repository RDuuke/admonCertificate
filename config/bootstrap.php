<?php

require_once "../vendor/autoload.php";
require_once BASE_DIR . "src" . DS ."Utils" . DS . "Utils.php";
use Certificate\Controllers\HomeController;
use Certificate\Controllers\CertificateController;
use Certificate\Controllers\AssociationController;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

$app = new Slim\App(include_once dirname(__DIR__) . DS . "config" . DS . "database.php");

$container = $app->getContainer();

$capsule = new Illuminate\Database\Capsule\Manager();

$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function () use ($capsule) {
    return $capsule;
};

$container['tmp'] = function() {
    return dirname(__DIR__)  . DS . "temp" . DS;
};
$container['files'] = function() {
    return dirname(__DIR__) . DS . "files" . DS;
};

$container['view'] = function ($container) {
    $view = new Twig(__DIR__ . "/../views", [
        'cache' => false,
    ]);
    $view->addExtension(new TwigExtension(
        $container->router,
        $container->request->getUri()
    ));
    $view->addExtension(new Knlv\Slim\Views\TwigMessages(
        new Slim\Flash\Messages()
    ));
    return $view;
};

$container['HomeController'] = function ($container) {
    return new HomeController($container);
};

$container['CertificateController'] = function ($container) {
    return new CertificateController($container);
};

$container['AssociationController'] = function ($container) {
    return new AssociationController($container);
};

require_once dirname(__DIR__) . "/src/router.php";

$app->run();