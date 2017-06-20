<?php
namespace cmsgears\elasticsearch\config;

// Yii Imports

// CMG Imports
use cmsgears\core\common\config\CmgProperties;

class ElasticProperties extends CmgProperties {

	const CONFIG_ELASTIC		= 'elasticsearch';

	const PROP_ACTIVE		= 'active';

	// Singleton instance
	private static $instance;

	// Constructor and Initialisation ------------------------------
	
	/**
	 * Return Singleton instance.
	 */
	public static function getInstance() {

		if( !isset( self::$instance ) ) {

			self::$instance	= new ElasticProperties();

			self::$instance->init( self::CONFIG_ELASTIC);
		}

		return self::$instance;
	}

	public function isActive() {
		
		return true;
		
	}
}
