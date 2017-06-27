<?php
namespace cmsgears\elasticsearch\services;

// Yii Imports
use Yii;

// CMG Imports
use cmsgears\core\common\services\base\EntityService;
use bizzlist\core\common\models\entities\Listing;
use yii\data\ActiveDataProvider ;
use yii\httpclient\Client;

class ElasticSearchService extends EntityService implements \cmsgears\elasticsearch\services\interfaces\IElasticSearchService {

	// Variables ---------------------------------------------------

	// Globals -------------------------------

	// Constants --------------

	// Public -----------------
	public $geoLocation;
	public $radius;
	
	// Protected --------------

	// Variables -----------------------------

	// Public -----------------

	// Protected --------------

	// Private ----------------

	// Traits ------------------------------------------------------

	// Constructor and Initialisation ------------------------------

	// Instance methods --------------------------------------------

	// Yii parent classes --------------------

	// yii\base\Component -----

	// CMG interfaces ------------------------

	// CMG parent classes --------------------

	// ConsoleTagService ---------------------

	// Data Provider ------

	// Read ---------------

	// Read - Models ---

	// Read - Lists ----

	// Read - Maps -----

	// Read - Others ---

	// Create -------------

	// Update -------------

	// Delete -------------

	// Static Methods ----------------------------------------------

	// CMG parent classes --------------------

	// ConsoleTagService ---------------------

	// Data Provider ------
	
	
	public function defaultSearch( $config = [] ) {

		$size = isset($config['size']) ? $config['size'] : 20;
		$from = isset($config['from']) ? $config['from'] : 0;
		
		$requests		= [];
		$geoLocation 	= Yii::$app->bzGeo->getCurrentGeoLocation();
		$lat			= $geoLocation->lat; 
		$long			= $geoLocation->long;	

		$radius			= Yii::$app->bzGeo->getRadius( 'listing' );
		
		$client = new Client(['requestConfig' => [ 'format' => Client::FORMAT_JSON ], 'responseConfig' => [  'format' => Client::FORMAT_JSON  ]]);
		
		//url
		$url = 'http://localhost:9200/listing/_search';
		
		$data =  "{
					\"query\": {
						\"bool\" : {
							\"should\" : {
								\"match\" : { \"status\" : 16000    }
							}
						}
					},
					\"from\": $from,
					\"size\": $size
				}";
	
		$requests[ 'search' ] = $client->get( $url, $data , $headers = [ 'content-type' => 'application/json' ], $options = [ ] );
		
		$response		= $client->batchSend( $requests );
		
		$responseData	= $response['search']->getData();
		
		$models			= $responseData['hits']['hits'];
		
		$modelsObjects = [];
		
		foreach( $models as $key => $content ) {
			
			$temp = [];
			$content = $content['_source'];
			
			$model			= new Listing();
			$model->name	= $content['name'];
			$model->slug	= $content['slug'];
			$model->type	= $content['type'];
			$model->widgetData = json_encode($content['widgetData']);
			
			$temp[ $key ] = $model;
			
			$modelsObjects[] = $temp[ $key ];
		}
		
		return $modelsObjects;
		
	}
	
	public function categoryIdSearch( $config = [] ) {

		$size = isset($config['size'])  ? $config['size'] : 15;
		$from = isset($config['from']) ? $config['from'] : 0;
				
		if( isset($config['categoryId']) ) {
			
			$categoryId = $config['categoryId'];

			$geoLocation 	= Yii::$app->bzGeo->getCurrentGeoLocation();
			$lat			= $geoLocation->lat; 
			$long			= $geoLocation->long;

			$client = new Client(['requestConfig' => [ 'format' => Client::FORMAT_JSON ], 'responseConfig' => [  'format' => Client::FORMAT_JSON  ]]);
			
			//search categoryIds 
			$url = 'http://localhost:9200/listing/_search';
			
			$data =  "{
					\"query\": {
						\"bool\" : {
							\"must\" : [
								{\"match\" : {\"categoryIds\" :$categoryId  }},
								{\"match\" : {\"status\" : 16000  }}
							]
						}
					},
					\"from\": $from,
					\"size\": $size
			}";
			
			$requests[ 'search' ] = $client->get( $url, $data , $headers = [ 'content-type' => 'application/json' ], $options = [ ] );
		
			$response		= $client->batchSend( $requests );

			$responseData	= $response['search']->getData();

			$models			= $responseData['hits']['hits'];

			$modelsObjects = [];

			foreach( $models as $key => $content ) {

				$temp = [];
				$content = $content['_source'];

				$model			= new Listing();
				$model->name	= $content['name'];
				$model->slug	= $content['slug'];
				$model->type	= $content['type'];
				$model->widgetData = json_encode($content['widgetData']);

				$temp[ $key ] = $model;

				$modelsObjects[] = $temp[ $key ];
			}

			return $modelsObjects;
		
		} else {
			return false;
		}
	}
	
	public function keywordSearch( $config = [] ) {
		//config
		$size		= isset($config['size']) ? $config['size'] : 20;
		$from		= isset($config['from']) ? $config['from'] : 0;
		$keyword	= isset($config['keyword']) ? $config['keyword'] : "";
		//geo points
		$geoLocation 	= Yii::$app->bzGeo->getCurrentGeoLocation();
		$lat			= $geoLocation->lat; 
		$long			= $geoLocation->long;
		$radius			= Yii::$app->bzGeo->getRadius( 'listing' );
		
		//search categoryIds 
		$url = 'http://localhost:9200/listing/data/_search';
		$data =  "{
			\"query\": {
				\"bool\" : {
					\"should\" : [
						{\"match\" : {\"title\" : \".$keyword.\"  }},
						{\"match\" : {\"summary\" : \".$keyword.\"  }},
						{\"match\" : {\"content\" : \".$keyword.\"  }},
						{\"match\" : {\"categories\" : \".$keyword.\"  }},
						{\"match\" : {\" tags\" : \".$keyword.\"  }}	
					],
					\"filter\" : {
						\"geo_distance\" : {
							\"distance\" : $radius,
							\"location\" : {
								\"lat\" : $lat,
								\"lon\" : $long
							}
						}
					}
				}
			},
			\"from\": $from,
			\"size\": $size
		}";
		$requests[ 'search' ] = $client->get( $url, $data , $headers = ['content-type' => 'application/json'], $options = [ ] );
		
		$response = $client->batchSend($requests);
		
		$models = $response['hits']['hits'];
		
		$modelsObjects = [];
		
		foreach( $models as $key => $model ) {
			
			$temp = [];
			$temp[ $key ] = json_decode(json_encode($model['_source']));
			$modelsData[] = $temp[ $key ];
		}
		
		return $modelsObjects;
		
	}
	
	
	public function listingSearch() {
		
		//request for search title 
		$url = 'http://localhost:9200/listing/data/_search';
		$data =  "{
			\"query\": {
				\"bool\" : {
					\"must\" : {
						\"match\" : {\"content.title\" : \"Shoe\"  }
					},
					\"filter\" : {
						\"geo_distance\" : {
							\"distance\" : \"100km\",
							\"location\" : {
								\"lat\" : $lat,
								\"lon\" : $long
							}
						}
					}
				}
			}, 
			\"size\": 15
		}";
		$requests[ 'search' ]	= $client->get( $url, $data , $headers = ['content-type' => 'application/json'], $options = [ ] );
		
		$response				= $client->batchSend($requests);
		
		return $response;		

	}
	
	
	public function dataProvider( $start , $chunk, $limit ) {
		
		$page = intval($start / $chunk);
		$end  = intval($limit / $chunk);
		$dataProvider = [];
		for( $page; $page < $end; $page++  ) {
			
			$provider = new ActiveDataProvider([
				'query' => Listing::find(),
				'pagination' => [
				'pageSize' => $chunk,
				'page' => $page,
				],
			]);
			
			// get the posts in the current page
			$models = $provider->getModels();
			foreach($models as $model){
				$dataProvider[] = $model;
			}
			
		}
		$result = $this->dataProcessing($dataProvider);
		return $result;

	}
	
	


	// Read ---------------

	// Read - Models ---

	// Read - Lists ----

	// Read - Maps -----

	// Read - Others ---

	// Create -------------

	// Update -------------

	// Delete -------------

}
