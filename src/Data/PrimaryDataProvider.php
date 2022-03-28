<?php

namespace WebBoMTool\Data;

use WebBoMTool\Data\Record;
use MediaWiki\MediaWikiServices;

class PrimaryDataProvider implements \BlueSpice\Data\IPrimaryDataProvider {

	/**
	 *
	 * @var \BlueSpice\Data\Record[]
	 */
	protected $data = [];

	/**
	 *
	 * @var \Wikimedia\Rdbms\IDatabase
	 */
	protected $db = null;

	/**
	 *
	 * @var \IContextSource
	 */
	protected $context = null;

	/**
	 *
	 * @var string
	 */
	private $webbomAPIUrl = "http://95.217.235.78:8081/api/v1";

	/**
	 *
	 * @param \Wikimedia\Rdbms\IDatabase $db
	 * @param \IContextSource $context
	 */
	public function __construct( $db, $context ) {
		$this->db = $db;
		$this->context = $context;
	}

	/**
	 *
	 * @param \BlueSpice\Data\ReaderParams $params
	 * @return array
	 */
	public function makeData( $params ) {
		$sNode = $this->context->getRequest()->getText( 'node' );

		$this->data = [];
		if ( $sNode === 'src' ) {
			$products =  $this->getProducts();

			foreach ( $products as $product ) {

				$oProduct = new \stdClass();
				$oProduct->text = $product->name;
				$oProduct->leaf = false;
				$oProduct->tracking = $product->version;
				$oProduct->id = 'src/' . str_replace( '/', '+', $oProduct->text );
				$this->data[] = new \BlueSpice\Data\Record( $oProduct );

			}
		} else {
			$products =  $this->getProducts();

			foreach ( $products as $product ) {
				if ( $sNode !== 'src/' . str_replace( '/', '+', $product->name ) ) {
					continue;
				}
				$compatibilityList = $this->getLicenseCompatibilityForProduct( $product->ID );
				foreach ( $product->components as $component ) {
					$oComponent = new \stdClass();
					$oComponent->text = $component->package ? $component->package : $component->name;
					$oComponent->leaf = true;
					$oComponent->tracking = $component->license->spdxId;
					$oComponent->id = 'src/' . $product->name . '/' . str_replace( '/', '+', $oComponent->text );
					$oComponent->compatible = $compatibilityList[$oComponent->text]["compatibility"];
					$oComponent->license = $compatibilityList[$oComponent->text]["license"];
					$this->data[] = new \BlueSpice\Data\Record( $oComponent );
				}
			}
		}

		return $this->data;
	}

	private function getProducts() {
		$components = [];

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $this->webbomAPIUrl . "/products" );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);

		$output_json = json_decode( $output );

		foreach ( $output_json as $component ) {
			$components[] = $component;
		}

		return $components;
	}

	private function getLicenseCompatibilityForProduct( $productId ) {
		$components = [];

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $this->webbomAPIUrl . "/lc/" . $productId );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);

		$output_json = json_decode( $output );
		
		error_log( var_export( $output, true ) );

		foreach ( $output_json->result as $compatibility ) {
			$package = $compatibility->Package->name;
			$components[$package]["compatibility"] = $compatibility->is_compatible;
			$components[$package]["license"] = $compatibility->Package->license;
		}

		return $components;
	}
}
