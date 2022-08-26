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

	public function getAllProjects()
	{
		$stmt = $this->pdo->prepare('SELECT PROJECT_ID as id, NAME as name 
		FROM PROJECT 
		ORDER BY name asc');

		$stmt->execute();

		return $stmt->fetchAll();
	}

	public function getAllCategories()
	{
		$stmt = $this->pdo->prepare('SELECT RLN_ID as id, NAME as name 
		FROM RESOURCE  as R
		INNER JOIN REQUIREMENT_FOLDER as RF on R.RES_ID = RF.RES_ID
		ORDER BY name asc');

		$stmt->execute();

		return $stmt->fetchAll();
    }

	public function getAllCategoriesByProjectId(string $value)
	{
		$stmt = $this->pdo->prepare('SELECT RLN_ID as id, NAME as name 
		FROM RESOURCE  as R
		INNER JOIN REQUIREMENT_FOLDER as RF on R.RES_ID = RF.RES_ID
		INNER JOIN REQUIREMENT_LIBRARY_NODE as RLN on RF.RLN_ID = RLN.RLN_ID
		AND PROJECT_ID = :value
		ORDER BY name asc');

		echo "SELECT RLN_ID as id, NAME as name 
		FROM RESOURCE  as R
		INNER JOIN REQUIREMENT_FOLDER as RF on R.RES_ID = RF.RES_ID
		INNER JOIN REQUIREMENT_LIBRARY_NODE as RLN on RF.RLN_ID = RLN.RLN_ID
	AND PROJECT_ID = $value
		ORDER BY name asc";
		$stmt->execute();

		return $stmt->fetchAll();
	}

	public function getAllVersions()
	{
		$stmt = $this->pdo->prepare('SELECT DISTINCT VALUE as name, VALUE as id
		FROM CUSTOM_FIELD_VALUE');
		$stmt->execute();

		return $stmt->fetchAll();
    }

	/**
	 *
	 */
	public function getAllIterations()
	{
		$stmt = $this->pdo->prepare('SELECT DISTINCT NAME as name, ITERATION_ID as id
		FROM ITERATION');
		$stmt->execute();

		return $stmt->fetchAll();

    }

	public function getAllRequirementsByCustomFieldValue(string $value)
	{
		$stmt = $this->pdo->prepare( 'SELECT CFV.BOUND_ENTITY_ID as id, RV.REFERENCE as reference, R.NAME as name, R.DESCRIPTION as description   
		FROM CUSTOM_FIELD_VALUE CFV
		LEFT JOIN REQUIREMENT_VERSION RV on RV.RES_ID = CFV.BOUND_ENTITY_ID
  		LEFT JOIN RESOURCE R on RV.RES_ID = R.RES_ID		
		WHERE BOUND_ENTITY_TYPE = "REQUIREMENT_VERSION"
		AND CFV.VALUE = :value
		ORDER BY RV.REFERENCE');

		$stmt->execute(array(':value' => $value));
		return $stmt->fetchAll();
    }

	public function getAllRequirementsByFolderId(int $folderId, string $status = 'APPROVED')
	{

		$stmt = $this->pdo->prepare('SELECT R_V.RES_ID as id, R_V.REFERENCE as reference,R.NAME as name,R.DESCRIPTION as description 
		FROM RESOURCE as R, REQUIREMENT_VERSION as R_V
		LEFT JOIN RLN_RELATIONSHIP as R_R on R_R.DESCENDANT_ID = R_V.RES_ID 
		WHERE R.RES_ID = R_V.RES_ID
		AND R_V.REQUIREMENT_STATUS = :status
		AND R_R.ANCESTOR_ID = :folderId
		');

		$stmt->execute(array(':status' => $status, ':folderId' => $folderId));
		return $stmt->fetchAll();
    }

	public function getRequirementById(int $requirementId)
	{
		$stmt = $this->pdo->prepare('SELECT R_V.RES_ID as id, R_V.REFERENCE as reference,R.NAME as name,R.DESCRIPTION as description 
		FROM RESOURCE as R, REQUIREMENT_VERSION as R_V
		LEFT JOIN RLN_RELATIONSHIP as R_R on R_R.DESCENDANT_ID = R_V.RES_ID 
		WHERE R.RES_ID = R_V.RES_ID
		AND R_V.RES_ID = :requirementId
		');

		$stmt->execute(array(':requirementId' => $requirementId));
		return $stmt->fetchAll();
    }

	public function getRequirementByReferenceId(string $referenceId)
	{
		$stmt = $this->pdo->prepare('SELECT R_V.RES_ID as id, R_V.REFERENCE as reference,R.NAME as name,R.DESCRIPTION as description 
		FROM RESOURCE as R, REQUIREMENT_VERSION as R_V
		LEFT JOIN RLN_RELATIONSHIP as R_R on R_R.DESCENDANT_ID = R_V.RES_ID 
		WHERE R.RES_ID = R_V.RES_ID
		AND R_V.REFERENCE = :referenceId
		');

		$stmt->execute(array(':referenceId' => $referenceId));
		return $stmt->fetchAll();
    }

    public function getAllRequirements($status = 'APPROVED', $category = 10)
    {
		$stmt = $this->pdo->prepare('SELECT R_V.RES_ID as id, R_V.REFERENCE as reference,R.NAME as name,R.DESCRIPTION as description 
		FROM RESOURCE as R, REQUIREMENT_VERSION as R_V
		WHERE R.RES_ID = R_V.RES_ID
		AND R_V.REQUIREMENT_STATUS = :status
		AND R_V.CATEGORY = :category;');

		$stmt->execute(array(':status' => $status, ':category' => $category));
		return $stmt->fetchAll();
    }

	public function getAllTestCaseForRequirement($requirementId)
	{
		$stmt = $this->pdo->prepare('SELECT TC.TCLN_ID as id, NAME as name, DESCRIPTION as description, REFERENCE as reference, PREREQUISITE as prerequisite
			FROM TEST_CASE TC
			LEFT JOIN REQUIREMENT_VERSION_COVERAGE RVC on TC.TCLN_ID = RVC.VERIFYING_TEST_CASE_ID
			LEFT JOIN TEST_CASE_LIBRARY_NODE TCLN on TC.TCLN_ID = TCLN.TCLN_ID
			WHERE RVC.VERIFIED_REQ_VERSION_ID = :requirement_id
			ORDER BY REFERENCE ASC;');

		$stmt->execute(array(':requirement_id'=> $requirementId));
		return $stmt->fetchAll();
    }

	public function getAllTestCaseForProject($projectId)
	{
		$stmt = $this->pdo->prepare('SELECT TC.TCLN_ID as id, TCLN.NAME as name, DESCRIPTION as description, REFERENCE as reference, PREREQUISITE as prerequisite
			FROM TEST_CASE TC
			LEFT JOIN ITERATION_TEST_PLAN_ITEM ITPI ON ITPI.TCLN_ID = TC.TCLN_ID
			LEFT JOIN TEST_CASE_LIBRARY_NODE TCLN on TC.TCLN_ID = TCLN.TCLN_ID
			WHERE TCLN.PROJECT_ID = :project_id
			ORDER BY REFERENCE ASC;');

		$stmt->execute(array(':project_id' => $projectId));
		return $stmt->fetchAll();
	}

	public function getAllTestCaseForIteration($iterationId)
	{
		$stmt = $this->pdo->prepare('SELECT TC.TCLN_ID as id, TCLN.NAME as name, DESCRIPTION as description, REFERENCE as reference, PREREQUISITE as prerequisite
			FROM TEST_CASE TC
			LEFT JOIN ITERATION_TEST_PLAN_ITEM ITPI ON ITPI.TCLN_ID = TC.TCLN_ID
			LEFT JOIN ITEM_TEST_PLAN_LIST ITPL ON ITPL.ITEM_TEST_PLAN_ID = ITPI.ITEM_TEST_PLAN_ID 
			LEFT JOIN TEST_CASE_LIBRARY_NODE TCLN on TC.TCLN_ID = TCLN.TCLN_ID
			WHERE ITPL.ITERATION_ID = :iteration_id
			ORDER BY REFERENCE ASC;');

		$stmt->execute(array(':iteration_id'=> $iterationId));
		return $stmt->fetchAll();
    }

	public function getAllTestStepForTestCase($testCaseId)
	{
		$stmt = $this->pdo->prepare('SELECT ACTION as action, EXPECTED_RESULT as expectedResult 
			FROM ACTION_TEST_STEP ATS
			LEFT JOIN TEST_CASE_STEPS TCS on ATS.TEST_STEP_ID = TCS.STEP_ID
			WHERE TCS.TEST_CASE_ID = :test_case_id
			ORDER BY TCS.STEP_ORDER');

		$stmt->execute(array(':test_case_id' => $testCaseId));
		return $stmt->fetchAll();
    }

    public function getAllTables()
    {
        $stmt = $this->pdo->prepare('SHOW TABLES;');
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
