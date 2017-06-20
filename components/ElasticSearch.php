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

		$factory->set( 'cmsgears\elasticsearch\services\interfaces\IElasticSearchService', 'cmsgears\elasticsearch\services\ElasticSearchService' );
	}

	public function initServices() {

		$factory = Yii::$app->factory->getContainer();

		$factory->set( 'elasticSearch', 'cmsgears\elasticsearch\services\ElasticSearchService' );
	}
}
