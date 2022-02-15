<?php

namespace WebBoMTool\Api\Store;

use WebBoMTool\Data\Store;

class Products extends \BlueSpice\Api\Store {

	protected function makeDataStore() {
		return new Store( $this->getContext(), $this->getServices()->getDBLoadBalancer() );
	}
}
