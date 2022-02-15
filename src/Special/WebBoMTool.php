<?php

namespace WebBoMTool\Special;

use BlueSpice\Special\ManagerBase;
use Html;
use SpecialPage;



class WebBoMTool extends ManagerBase {

	public function __construct() {
		parent::__construct( 'WebBoMTool', 'read' );
	}

	/**
	 * @return string ID of the HTML element being added
	 */
	protected function getId() {
		return 'bs-webbomtooloverview-grid';
	}

	/**
	 * @return array
	 */
	protected function getModules() {
		return [
			"ext.webBoMTool.productoverview"
		];
	}
}
