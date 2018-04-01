<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ricardo GarcÃ­a
 * Date: 18/03/2018
 * Time: 01:40 AM
 */

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once 'vendor/autoload.php';
require_once 'mysqlConnection.php';

// Necessary headers
header('Content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];

if($method == "OPTIONS")
{
    die();
}

$app = new \Slim\Slim();

/**
 * Get all category list
 */
$app->get('/categories',function() use ($app) {
    $query = "SELECT * FROM category;";
    $result = Connexion::getInstance()->connect()->query($query,PDO::FETCH_OBJ);
    echo json_encode($result->fetchAll());
});

$app->run();