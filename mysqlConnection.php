<?php
/**
 * Created by PhpStorm.
 * User: Ricardo
 * Date: 07/03/2018
 * Time: 05:39 PM
 */

$servername = "localhost";
$bdd = "cabreras1u_bikeeShop";
$username = "cabreras1u_appli";
$password = "31723548";

try {
    $db_connection = new PDO("mysql:host=$servername;dbname=$bdd", $username, $password,array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
    $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
}
