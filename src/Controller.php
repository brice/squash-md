<?php

use View\View;
use Model\Model;

class Controller
{
    protected $config;
    
    public function __construct($config)
    {
        $pdo  = new PDO($config['dsn'], $config['db_user'], $config['db_pass']);
        $this->model = new Model($pdo);
        $this->view  = new View();
    }

    public function Main()
    {
        $data = $this->model->getAllTables();

        return $this->view->format($data);
    }


    
}