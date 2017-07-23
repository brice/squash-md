<?php

namespace Model;

use \PDO;

class Model
{
    protected $pdo;

    /**
     * Model constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllTestCase()
    {

    }

    public function getAllTables()
    {
        $stmt = $this->pdo->prepare('SHOW TABLES;');
        $stmt->execute();

        return $stmt->fetchAll();
    }
}