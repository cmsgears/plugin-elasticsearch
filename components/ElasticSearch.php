<?php
namespace cmsgears\elasticsearch\components;

// Yii Imports
use Yii;

class ElasticSearch extends \yii\base\Component {

	// Global -----------------

	// Public -----------------

	// Protected --------------

	// Private ----------------

	// Constructor and Initialisation ------------------------------

	public function init() {

		parent::init();

		// Register console components and objects
		$this->registerComponents();
	}

	// Instance methods --------------------------------------------

	// Yii parent classes --------------------

	// CMG parent classes --------------------

	// Core ----------------------------------

	// Properties

	// Components and Objects

	public function registerComponents() {

		// Register services
		$this->registerServices();

		// Init services
		$this->initServices();
	}

	public function registerServices() {

		$factory = Yii::$app->factory->getContainer();

		$factory->set( 'cmsgears\elasticsearch\services\interfaces\system\IElasticSearchService', 'cmsgears\elasticsearch\services\system\ElasticSearchService' );
	}

	public function initServices() {

		$factory = Yii::$app->factory->getContainer();

		$factory->set( 'elasticSearchService', 'cmsgears\elasticsearch\services\system\ElasticSearchService' );
	}
}
