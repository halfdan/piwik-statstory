<?php
require '../vendor/autoload.php';

use Slim\Slim;
use Slim\Extras\Views\Twig as Twig;

$app = new Slim(array(
    'view' => new Twig
));

$app->get('/', function () {
    echo "Hello, $name";
});


$app->run();
