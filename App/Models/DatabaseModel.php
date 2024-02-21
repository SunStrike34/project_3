<?php

namespace App\Models;

use PDOException;
use PDO;

class DatabaseModel
{
    private static $instance = null;
    private $pdo, $query, $error = false, $results, $count;

    public function __construct(PDO $pdo)
    {
        try {
            $this -> pdo = $pdo;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public static function getInstance(PDO $pdo)
    {
        if (!isset(self::$instance)) {
            self::$instance = new DatabaseModel($pdo);
        }

        return self::$instance;
    }

    public function getOne(string $table, array $columns)
    {

    }

    public function getAll()
    {

    }

    public function insert()
    {

    }

    public function update()
    {
        
    }

    public function delete()
    {

    }
}