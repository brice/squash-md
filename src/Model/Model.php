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

    public function getAllRequirements($status = 'APPROVED')
    {
		$stmt = $this->pdo->prepare('SELECT R_V.RES_ID as id, R_V.REQUIREMENT_STATUS as status, R_V.REFERENCE as reference,R.NAME as name,R.DESCRIPTION as description 
		FROM RESOURCE as R, REQUIREMENT_VERSION as R_V
		WHERE R.RES_ID = R_V.RES_ID
		AND R_V.REQUIREMENT_STATUS = :status;');

		$stmt->execute(array(':status' => $status));
		return $stmt->fetchAll();
    }

	public function getAllTestCaseForRequirement($requirementId)
	{
		$stmt = $this->pdo->prepare('SELECT TC.TCLN_ID as id, NAME as name, DESCRIPTION as description, REFERENCE as reference, PREREQUISITE as prerequisite
			FROM TEST_CASE TC
			LEFT JOIN REQUIREMENT_VERSION_COVERAGE RVC on TC.TCLN_ID = RVC.VERIFYING_TEST_CASE_ID
			LEFT JOIN TEST_CASE_LIBRARY_NODE TCLN on TC.TCLN_ID = TCLN.TCLN_ID
			WHERE RVC.VERIFIED_REQ_VERSION_ID = :requirement_id;');

		$stmt->execute(array(':requirement_id'=> $requirementId));
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