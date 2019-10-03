<?php

use View\SummaryView;
use View\View;
use Model\Model;

class Controller
{
    protected $config;

    protected $model;

    protected $view;

    public function __construct($config)
    {
        $pdo  = new PDO($config['dsn'], $config['db_user'], $config['db_pass']);
        $this->model = new Model($pdo);
		$view = $_GET['view'] ?? 'summary';

		if ($view == 'summary') {
			$this->view  = new SummaryView();
		} else {
			$this->view  = new View();
		}
    }

    public function Main()
    {
        $action = $_GET['action'] ?? 'default';
		$id = $_GET['id'] ?? 0;
		$referenceIds = $_GET['referenceIds'] ?? [];
		$requirementId = $_GET['requirement'] ?? null;

        switch($action) {
			case 'display_test_case':
				return $this->displayTestCase($referenceIds);
			case 'list':
				return $this->returnListRequirements($id);
			case 'report':
				return $this->returnDefaultReport($id, $requirementId);
            default:
				return $this->displaySummary();
        }
    }

    protected function displayTestCase($referenceIds)
	{
		$return = '';
		foreach ($referenceIds as $id) {
			$data = $this->model->getRequirementByReferenceId($id);

			foreach ($data as &$requirement) {
				$requirement['cases'] = $this->model->getAllTestCaseForRequirement($requirement['id']);
				foreach ($requirement['cases'] as & $case) {
					$case['step'] = $this->model->getAllTestStepForTestCase($case['id']);
				}
			}

			$return .= $this->view->format($data)->write();
		}

		return $return;
	}

	protected function displaySummary()
	{
		$sortBy= $_GET['sort'] ?? 'default';

		if ($sortBy == "version") {
			$this->view->setAction('list');
			$data = $this->model->getAllVersions();
		} else {
			$this->view->setAction('report');
			$data = $this->model->getAllCategories();
		}

		return $this->view->format($data)->write();
    }

    protected function returnDefaultReport(int $folderId, int $requirementId = null)
	{
		// First of all, we get all requirements
		if ($folderId == 0) {
			$data = $this->model->getAllRequirements();
		} else if ($requirementId != null) {
			$data = $this->model->getRequirementById($requirementId);
		} else {
			$data = $this->model->getallRequirementsByFolderId($folderId);
		}

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

    protected function returnListRequirements(string $value)
	{
    	$data = $this->model->getAllRequirementsByCustomFieldValue($value);

		if (isset($_GET['export'])) {
			return $this->view->format($data)->write();
		}
		return $this->view->format($data)->writeHTML();
	}
}
