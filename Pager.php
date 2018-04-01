<?php

require_once './mysqlConnection.php';

class Pager {

	public static function select(int $page, int $limit)
	{
        $debut = ($page - 1) * $limit;
		$query = "SELECT SQL_CALC_FOUND_ROWS * FROM products LIMIT :limit OFFSET :debut";
        $result = Connexion::getInstance()->connect()->prepare($query);
        $result->bindValue('debut', $debut, PDO::PARAM_INT);
        $result->bindValue('limit', $limit, PDO::PARAM_INT);
        $result->execute();
		return $result;
	}

    public static function selectBy(int $byElement,int $page, int $limit)
    {
        $debut = ($page - 1) * $limit;
        $query = "SELECT SQL_CALC_FOUND_ROWS * FROM products WHERE idCategory=:id_category LIMIT :limit OFFSET :debut";
        $result = Connexion::getInstance()->connect()->prepare($query);
        $result->bindValue('id_category',$byElement,PDO::PARAM_INT);
        $result->bindValue('debut', $debut, PDO::PARAM_INT);
        $result->bindValue('limit', $limit, PDO::PARAM_INT);
        $result->execute();
        return $result;
    }
}