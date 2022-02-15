<?php

namespace WebBoMTool\Hook\BeforePageDisplay;

use BlueSpice\Hook\BeforePageDisplay;

class AddModules extends BeforePageDisplay {

	protected function doProcess() {
		$this->out->addModuleStyles( 'ext.webBoMTool.styles' );
		$this->out->addModules( 'ext.webBoMTool' );

		return  true;
	}
}
