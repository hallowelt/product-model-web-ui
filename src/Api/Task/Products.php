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
		'scanner',  //2
		'import',   //3
		'download', //1
		'initialise',//aĺl
		'delete'
	];

	private $webbomAPIUrl = "http://95.217.235.78:8081/api/v1";

	/**
	 * Downloads, scans and imports a given project.
	 * @param stdClass $taskData
	 * @param array $params
	 * @return Standard
	 */
	protected function task_initialise( $taskData, $params ) {
		$oReturn = $this->makeStandardReturn();
		$oReturn = $this->task_download( $taskData, $params );
		$oReturn = $this->task_scanner( $taskData, $params );
		$oReturn = $this->task_import( $taskData, $params );

		return $oReturn;
	}
	/**
	 * Scans the plugin output for a given project
	 * @param stdClass $taskData
	 * @param array $params
	 * @return Standard
	 */
	protected function task_scanner( $taskData, $params ) {
		$oReturn = $this->makeStandardReturn();

		$data = array(
			'scannerName' => 'phpscanner',
			'source' => $this->makeSourcePathFromUrl( $taskData->url ), //'/opt/formengine/source',
			'output' => $this->makeOutputPathFromUrl( $taskData->url ) //'/opt/formengine/output'
		);
		
		$data = json_encode( $data );

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $this->webbomAPIUrl . "/scanner" );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		curl_close($ch);
		
		//error_log( var_export($output, true));

		$oReturn->success = true;
		$oReturn->message = $output; //wfMessage( 'bs-pagetemplates-tpl-edited' )->plain();

		return $oReturn;
	}


	/**
	 * Imports the plugin scanner result for a given project
	 * @param stdClass $taskData
	 * @param array $params
	 * @return Standard
	 */
	protected function task_import( $taskData, $params ) {
		$oReturn = $this->makeStandardReturn();

		$data = array(
			'importType' => 'scanner',
			'importPath' => $this->makeOutputPathFromUrl( $taskData->url ) . '/phpScanner.json' //'/opt/formengine/output/phpScanner.json'
		);
		
		$data = json_encode( $data );

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $this->webbomAPIUrl . "/products/import" );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		curl_close($ch);
		
		//error_log( var_export($output, true));

		$oReturn->success = true;
		$oReturn->message = $output; //'Import started'; //wfMessage( 'bs-pagetemplates-tpl-edited' )->plain();


		return $oReturn;
	}

	/**
	 * Downloads the source for a new project to analyze
	 * @param stdClass $taskData
	 * @param array $params
	 * @return Standard
	 */
	protected function task_download( $taskData, $params ) {
		$oReturn = $this->makeStandardReturn();
		
		$data = array(
			'url' => $taskData->url, //https://github.com/hallowelt/mwstake-mediawiki-component-formengine
			'path' => $this->makeSourcePathFromUrl( $taskData->url ) //'/opt/formengine/source'
		);
		
		$data = json_encode( $data );

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $this->webbomAPIUrl . "/download" );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		curl_close($ch);
		
		//error_log( var_export($output, true));

		$oReturn->success = true;
		$oReturn->message = $output; //wfMessage( 'bs-pagetemplates-tpl-edited' )->plain();


		return $oReturn;
	}

	/**
	 * Deletes one project
	 * @param stdClass $taskData
	 * @param array $params
	 * @return Standard
	 */
	protected function task_delete( $taskData, $params ) {
		$oReturn = $this->makeStandardReturn();

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $this->webbomAPIUrl . "/products/" . $taskData->bomId );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "DELETE" );
		$output = curl_exec($ch);
		curl_close($ch);

		$oReturn->success = true;
		$oReturn->message = $output; //wfMessage( 'bs-pagetemplates-tpl-edited' )->plain();


		return $oReturn;
	}

	/**
	 * Returns an array of tasks and their required permissions
	 * array( 'taskname' => array('read', 'edit') )
	 * @return array
	 */
	protected function getRequiredTaskPermissions() {
		return [
			'scanner' => [ 'wikiadmin' ],
			'import' => [ 'wikiadmin' ],
			'initialise' => [ 'wikiadmin' ],
			'download' => [ 'wikiadmin' ],
			'export' => [ 'wikiadmin' ],
			'delete' => [ 'wikiadmin' ]
		];
	}
	
	/**
	 * Returns a path segment from a given url
	 * @param string $url
	 * @return string
	 */
	protected function makePathFromUrl( $url ) {
		$path = str_replace( "https://", "", $url );
		$path = str_replace( "http://", "", $path );

		return $path;
	}

	/**
	 * Returns the path used for storing the code source from a given url
	 * @param string $url
	 * @return string
	 */
	protected function makeSourcePathFromUrl( $url ) {
		$path = $this->makePathFromUrl( $url );
		$path = '/opt/analysis/' . $path . '/source';
		
		return $path;
	}

	/**
	 * Returns the path used for gettint the plugin output from a given url
	 * @param string $url
	 * @return string
	 */
	protected function makeOutputPathFromUrl( $url ) {
		$path = $this->makePathFromUrl( $url );
		$path = '/opt/analysis/' . $path . '/output';
		
		return $path;
	}
}
