<?php


require_once 'vendor/autoload.php';
require_once './mysqlConnection.php';
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// Necessary headers
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET");
header('Content-type: application/json; charset=utf-8');
header("Allow: GET");
$method = $_SERVER['REQUEST_METHOD'];

if($method == "OPTIONS")
{
    die();
}

$app = new \Slim\Slim();

$app->post("/orders",function() use ($app) {
    $data_request = json_decode($app->request->post('data_request'),true);

    $product_list = $data_request['product_list'];
    $product_quantity = $data_request['product_quantity'];
    $total_price = $data_request['total_price'];
    $user = $data_request['user'];

    $db_connection = Connexion::getInstance()->connect();
    
    // inserting user in 'users' table
    $query = "INSERT INTO user (idUser, name, lastname, addresse, email)
                VALUES (DEFAULT, :name, :lastname, :address, :email)";

    $insert_task = $db_connection->prepare($query);
    $insert_task->bindValue(':name',     $user['name'],     PDO::PARAM_STR);
    $insert_task->bindValue(':lastname', $user['lastname'], PDO::PARAM_STR);
    $insert_task->bindValue(':address',  $user['address'],  PDO::PARAM_STR);
    $insert_task->bindValue(':email',    $user['email'],    PDO::PARAM_STR);
    $insert_task->execute();
    $id_user = $db_connection->lastInsertId();

    // inserting order in 'orders' table
    $date = (new \DateTime())->format('Y-m-d H:i:s');
    $query = "INSERT INTO orders (idOrder, idUser, date, payment)
                VALUES(DEFAULT, :idUser, :date, :payment)";
    $insert_task = $db_connection->prepare($query);
    $insert_task->bindValue(':idUser',  $id_user,     PDO::PARAM_STR);
    $insert_task->bindValue(':date',  $date,          PDO::PARAM_STR);
    $insert_task->bindValue(':payment', $total_price, PDO::PARAM_STR);
    $insert_task->execute();
    $id_order = $db_connection->lastInsertId();
    
    // inserting products in 'description_order' table
    $rows_inserted = 0;
    $index = 0;
    foreach ($product_list as $data) {
        $query = "INSERT INTO description_order (
                    idDescriptionOrder,idOrder,idProduct,quantity
                )
                VALUES (DEFAULT,:idOrder,:idProduct,:quantity);";
        $insert_task = $db_connection->prepare($query);
        $insert_task->bindValue(':idOrder',       $id_order,                 PDO::PARAM_STR);
        $insert_task->bindValue(':idProduct',     $data,                     PDO::PARAM_STR);
        $insert_task->bindValue(':quantity',      $product_quantity[$index], PDO::PARAM_STR);
        $insert_task->execute();
        if ($insert_task->rowCount() > 0)
        {
            $rows_inserted ++;
        }
        $index ++;

        // updating product quantity for each one
        $query = "UPDATE products SET quantity = IFNULL(quantity,1) - 1 WHERE idProduct = $data";
        $insert_task = $db_connection->prepare($query);
        $insert_task->execute();
    }

    $result = [
        'rows_to_insert' => sizeof($product_list),
        'rows_inserted'  => $rows_inserted,
    ];

    echo json_encode($result);
});

$app->run();