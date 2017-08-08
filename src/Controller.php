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
                return $this->returnDefaultReport();
        }
    }

    protected function returnDefaultReport() {
    	// First of all, we get all requirements
        $data = $this->model->getAllRequirements();

        foreach ($data as &$requirement) {
			$requirement['cases'] = $this->model->getAllTestCaseForRequirement($requirement['id']);
			foreach ($requirement['cases'] as & $case) {
				$case['step'] = $this->model->getAllTestStepForTestCase($case['id']);
			}
		}


        if (isset($_GET['export'])) {
            return $this->view->format($data)->write();

        }
        return $this->view->format($data)->writeHTML();
    }


    
}