<?php
/**
 * Created by PhpStorm.
 * User: Ricardo
 * Date: 07/03/2018
 * Time: 05:39 PM
 */

class Connexion {
    private $servername;
    private $bdd;
    private $username;
    private $password;
    private static $_instance;

    private function __construct(){
        $this->servername = "localhost";
        $this->bdd = "cabreras1u_bikeeShop";
        $this->username = "cabreras1u_appli";
        $this->password = "31723548";
        self::$_instance = null;
    }

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Connexion();
        }
        return self::$_instance;
    }


    public function connect()
    {
        try {
            $db_connection = new PDO("mysql:host=$this->servername;dbname=$this->bdd", $this->username, $this->password,array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
            $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e)
        {
            echo "Connection failed: " . $e->getMessage();
        }
        return $db_connection;
    }
}
