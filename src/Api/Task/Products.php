<?php
namespace WebBoMTool\Api\Task;
use BlueSpice\Api\Response\Standard;

/**
 * GroupManager Api class
 * @package BlueSpice_Extensions
 */
class Products extends \BSApiTasksBase {

	/**
	 * Methods that can be called by task param
	 * @var array
	 */
	protected $aTasks = [
		'startScan',
		'import',
		'initialise',
		'export'
	];

	/**
	 * Creates or changes a template
	 * @param stdClass $taskData
	 * @param array $params
	 * @return Standard
	 */
	protected function task_startScan( $taskData, $params ) {
		$oReturn = $this->makeStandardReturn();



		$oReturn->success = true;
		$oReturn->message = 'Scan started'; //wfMessage( 'bs-pagetemplates-tpl-edited' )->plain();


		return $oReturn;
	}

	/**
	 * Deletes one or several templates
	 * @param stdClass $taskData
	 * @param array $params
	 * @return Standard
	 */
	protected function task_import( $taskData, $params ) {
		$oReturn = $this->makeStandardReturn();



		$oReturn->success = true;
		$oReturn->message = 'Import started'; //wfMessage( 'bs-pagetemplates-tpl-edited' )->plain();


		return $oReturn;
	}
	/**
	 * Deletes one or several templates
	 * @param stdClass $taskData
	 * @param array $params
	 * @return Standard
	 */
	protected function task_initialise( $taskData, $params ) {
		$oReturn = $this->makeStandardReturn();



		$oReturn->success = true;
		$oReturn->message = 'Initialise started'; //wfMessage( 'bs-pagetemplates-tpl-edited' )->plain();


		return $oReturn;
	}
	/**
	 * Deletes one or several templates
	 * @param stdClass $taskData
	 * @param array $params
	 * @return Standard
	 */
	protected function task_export( $taskData, $params ) {
		$oReturn = $this->makeStandardReturn();



		$oReturn->success = true;
		$oReturn->message = 'Export started'; //wfMessage( 'bs-pagetemplates-tpl-edited' )->plain();


		return $oReturn;
	}
	/**
	 * Returns an array of tasks and their required permissions
	 * array( 'taskname' => array('read', 'edit') )
	 * @return array
	 */
	protected function getRequiredTaskPermissions() {
		return [
			'startScan' => [ 'wikiadmin' ],
			'import' => [ 'wikiadmin' ],
			'initialise' => [ 'wikiadmin' ],
			'export' => [ 'wikiadmin' ],
		];
	}
}
