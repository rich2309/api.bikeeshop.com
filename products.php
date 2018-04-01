<?php
/**
 * Created by PhpStorm.
 * User: Ricardo
 * Date: 07/03/2018
 * Time: 05:39 PM
 */

require_once 'vendor/autoload.php';
require_once './Pager.php';
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


/**
 * Get all product list
 */
$app->get('/products/page/:page/limit/:limit', function($page, $limit) {
    $result = Pager::select(intval($page),intval($limit));
    echo json_encode($result->fetchAll());
});

/**
 * URL for get a product by id
 */
$app->get('/products/:id_product',function($id_product) {
    $query = "SELECT * FROM products WHERE idProduct=:id_product";
    $read_task = Connexion::getInstance()->connect()->prepare($query);
    $read_task->bindValue(':id_product',$id_product,PDO::PARAM_INT);
    $read_task->execute();
    echo json_encode($read_task->fetchObject());
});

/**
 * URL for get a list of products by category
 */
$app->get('/products/category/:id_category/page/:page/limit/:limit',function($id_category,$page,$limit) {
    $result = Pager::selectBy(intval($id_category),intval($page),intval($limit));
    echo json_encode($result->fetchAll());
});

/**
 * URL for add products
 */
$app->post("/products",function() use ($app) {
    $data_request = $app->request->post('data_request');
    $array_data = json_decode($data_request,true);

    $query = "INSERT INTO products (
                    idProduct,name,price,description,url_img,idCategory
                )
                VALUES (DEFAULT ,:name,:price,:description,:url_img,:id_category);";
    $insert_task = Connexion::getInstance()->connect()->prepare($query);

    try {
        $insert_task->bindValue(':name',       $array_data['_name'],       PDO::PARAM_STR);
        $insert_task->bindValue(':price',      $array_data['_price'],      PDO::PARAM_STR);
        $insert_task->bindValue(':description',$array_data['_description'],PDO::PARAM_STR);
        $insert_task->bindValue(':id_category',$array_data['_idCategory'],PDO::PARAM_STR);

        if (isset($array_data['_url_img'])) {
            $insert_task->bindValue(':url_img',  $array_data['_url_img'],  PDO::PARAM_STR);
        } else {
            $insert_task->bindValue(':url_img',  'no image selected',PDO::PARAM_STR);
        }

        $insert_task->execute();
        $response_from_db = ($insert_task->rowCount() > 0);

        $response_params = array(
            'success_code'    => 201,
            'success_message' => 'Product added successfully',
            'error_code'      => 400,
            'error_message'   => 'Product not added. Please check required data and retry'
        );

        $result = evaluateOperation($response_from_db,$response_params);
    } catch (Exception $exception) {
        $result = array(
            "Status code" => $exception->getCode(),
            "Message"     => $exception->getMessage()
        );
    }
    echo json_encode($result);
});

/**
 * URL for update products
 */
$app->put("/products/:id_product",function($id_product) use ($app) {
    $data_request = $app->request->post("data_request");
    $array_data = json_decode($data_request,true);

    $query = "UPDATE product SET name=:name, description=:description, price=:price, url_image=:url_image WHERE id=:id;";
    $update_task = Connexion::getInstance()->connect()->prepare($query);
    try {
        $update_task->bindValue(':name',       $array_data['name'],       PDO::PARAM_STR);
        $update_task->bindValue(':description',$array_data['description'],PDO::PARAM_STR);
        $update_task->bindValue(':price',      $array_data['price'],      PDO::PARAM_STR);
        $update_task->bindValue(':url_image',  $array_data['url_image'],  PDO::PARAM_STR);
        $update_task->bindValue(':id',         $id_product,               PDO::PARAM_STR);
        $update_task->execute();
        $response_from_db = ($update_task->rowCount() > 0);

        $response_params = array(
            'success_code'    => 201,
            'success_message' => 'Product updated successfully',
            'error_code'      => 400,
            'error_message'   => 'Product not updated. Please check required data and retry'
        );

        $result = evaluateOperation($response_from_db,$response_params);
    } catch (PDOException $exception) {
        $result = array(
            "Status code" => $exception->getCode(),
            "Message"     => $exception->getMessage()
        );
    }
    echo json_encode($result);
});

/**
 *  URL for delete products
 */
$app->delete('/products/:id_product',function($id_product) use ($app){
    $query = "DELETE FROM product WHERE id=:id_product";
    $delete_task = Connexion::getInstance()->connect()->prepare($query);

    try {
        $delete_task->bindValue(':id_product',$id_product,PDO::PARAM_INT);
        $delete_task->execute();
        $response_from_db = ($delete_task->rowCount() > 0);

        $response_params = array(
            'success_code'    => 200,
            'success_message' => 'Product deleted successfully',
            'error_code'      => '400',
            'error_message'   => 'Product not deleted. Please check required data and retry'
        );

        $result = evaluateOperation($response_from_db,$response_params);
    } catch (PDOException $exception) {
        $result = array(
            "Status code" => $exception->getCode(),
            "Message"     => $exception->getMessage()
        );
    }
    echo json_encode($result);
});



/**
 * @param string $type_operation CRUD operation type
 * @param bool $operationStatus Response received from DB
 * @return array Result of db operation to show in JSON format
 */
function evaluateOperation(bool $operationStatus, array $response_params):array {
    if ($operationStatus) {
        return array(
            "Status code" => $response_params['success_code'],
            "Message"     => $response_params['success_message']
        );
    } else {
        return  array(
            "Status code" => $response_params['error_code'],
            "Message"     => $response_params['error_message']
        );
    }
}

$app->run();