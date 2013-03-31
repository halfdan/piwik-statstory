<?php
require '../vendor/autoload.php';
require '../rb.php';

use Slim\Slim;
use Slim\Extras\Views\Twig as Twig;
use Knp\PiwikClient as PiwikClient;


Twig::$twigTemplateDirs = array("../templates");

// set up database connection
R::setup('mysql:host=localhost;dbname=statstory','root','');

$app = new Slim(array(
    'view' => new Twig("../templates")
));

/**
 * Landing page
 */
$app->get('/', function () use ($app) {
    $app->render('index.twig');
});

/**
 * Website listing.
 */
$app->get('/sites', function() use ($app) {
    $sites = R::findAll('site');
    $app->render('sites/index.twig', array('sites' => $sites));
});

$app->post('/sites', function () use ($app) {
    $site = R::dispense('site');

    // Fetch POST parameters
    $req = $app->request();
    $site->name = $req->post('piwik_name');
    $site->url = $req->post('piwik_url');
    $site->token = $req->post('piwik_token');

    // Store new website
    $id = R::store($site);

    // Redirect to new website listing
    $app->redirect(sprintf('/sites/%d', $id));
});

$app->get('/sites/create', function() use ($app) {
    $app->render('sites/create.twig');
});

$app->get('/sites/:id', function($id) use ($app) {
    $site = R::load('site', $id);
    $app->render('sites/show.twig', array('site' => $site));
});

$app->get('/sites/:id/availableReports', function($id) use ($app) {
    $site = R::load('site', $id);
    $connection = new PiwikClient\Connection\HttpConnection($site->url);
    $client     = new PiwikClient\Client($connection, $site->token);

    echo $client->call('API.getReportMetadata', array(), 'json');
});

$app->get('/reports/create', function() use ($app) {
    
    $app->render('reports/create.twig', array('sites' => $sites));
});

$app->run();
