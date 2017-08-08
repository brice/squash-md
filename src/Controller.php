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
        $action = $_GET['action'] ?? 'default';
        switch($action) {
            default:
                return $this->returnFormatedTables();
        }
    }

    protected function returnFormatedTables() {
        $data = $this->model->getAllTables();

        if (isset($_GET['export'])) {
            return $this->view->format($data)->write();

        }
        return $this->view->format($data)->writeHTML();
    }


    
}