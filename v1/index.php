<?php

require '../Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(array(
    'debug' => true
));

$app->get('/hello:name', function($name) use ($app) {
    echo "in get funciton";
    echo "hello $name";
});

$app->get('/:name', function($name) use ($app) {
    echo "in get funciton";
    echo "hello $name";
});

$app->get('/', function() use ($app) {
    echo "in get funciton";
    echo "hello";
});

$app->run();
?>

