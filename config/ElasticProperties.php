<?php
namespace cmsgears\elasticsearch\config;

// CMG Imports
use cmsgears\core\common\config\CmgProperties;

class ElasticProperties extends CmgProperties {

	// Variables ---------------------------------------------------

	// Global -----------------

	const CONFIG_ELASTIC	= 'elasticsearch';

	const PROP_ACTIVE		= 'active';

	const PROP_RESOUCE		= 'resource';

	const PROP_URL			= 'url';

	const PROP_PORT			= 'port';

	const PROP_RESOUCE_PATH	= 'resource_path';

	// Public -----------------

	// Protected --------------

	// Private ----------------

	// Singleton instance
	private static $instance;

	// Constructor and Initialisation ------------------------------

	/**
	 * Return Singleton instance.
	 */
	public static function getInstance() {

		if( !isset( self::$instance ) ) {

			self::$instance	= new ElasticProperties();

			self::$instance->init( self::CONFIG_ELASTIC );
		}

		return self::$instance;
	}

	// Instance methods --------------------------------------------

	// Yii parent classes --------------------

	// CMG parent classes --------------------

	// ElasticProperties ---------------------

	// Properties

	public function isActive() {

		return $this->properties[ self::PROP_ACTIVE ];
	}

	public function isResource() {

		return $this->properties[ self::PROP_RESOUCE ];
	}

	public function getUrl() {

		return $this->properties[ self::PROP_URL ];
	}

	public function getPort() {

		return $this->properties[ self::PROP_PORT ];
	}

	public function getResourcePath() {

		return $this->properties[ self::PROP_RESOUCE_PATH ];
	}

	public function getPath() {

		if( $this->isResource() ) {

			return $this->getResourcePath();
		}

		return self::$instance->properties[ self::PROP_URL ] . ':' . self::$instance->properties[ self::PROP_PORT ];
	}
}
