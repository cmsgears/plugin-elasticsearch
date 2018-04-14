<?php
// CMG Imports
use cmsgears\core\common\config\CoreGlobal;

use cmsgears\core\common\models\entities\Site;
use cmsgears\core\common\models\entities\User;
use cmsgears\core\common\models\resources\Form;
use cmsgears\core\common\models\resources\FormField;

use cmsgears\core\common\utilities\DateUtil;

class m160822_061228_elasticsearch extends \yii\db\Migration {

	// Public Variables

	// Private Variables

	private $prefix;

    private $site;
    private $master;

    public function init() {

		// Table prefix
		$this->prefix	= Yii::$app->migration->cmgPrefix;

		$this->site		= Site::findBySlug( CoreGlobal::SITE_MAIN );
		$this->master	= User::findByUsername( Yii::$app->migration->getSiteMaster() );

        Yii::$app->core->setSite( $this->site );
    }

    public function up() {

        // Create various config
        $this->insertElasticConfig();

        // Init default config
        $this->insertDefaultConfig();
    }

    private function insertElasticConfig() {

        $this->insert( $this->prefix . 'core_form', [
            'siteId' => $this->site->id,
            'createdBy' => $this->master->id, 'modifiedBy' => $this->master->id,
            'name' => 'Config Elasticsearch', 'slug' => 'config-elasticsearch',
            'type' => CoreGlobal::TYPE_SYSTEM,
            'description' => 'Elasticsearch configuration form.',
            'success' => 'All configurations saved successfully.',
            'captcha' => false,
            'visibility' => Form::VISIBILITY_PROTECTED,
            'status' => Form::STATUS_ACTIVE, 'userMail' => false,'adminMail' => false,
            'createdAt' => DateUtil::getDateTime(),
            'modifiedAt' => DateUtil::getDateTime()
        ]);

        $config	= Form::findBySlug( 'config-elasticsearch', CoreGlobal::TYPE_SYSTEM );

        $columns = [ 'formId', 'name', 'label', 'type', 'compress', 'validators', 'order', 'icon', 'htmlOptions' ];

        $fields	= [
            [ $config->id, 'active', 'Active', FormField::TYPE_TOGGLE, false, 'required', 0, NULL, '{"title":"Active"}' ],
			[ $config->id, 'resource', 'Resource', FormField::TYPE_TOGGLE, false, 'required', 0, NULL, '{"title":"Resource"}' ],
			[ $config->id, 'url', 'Url', FormField::TYPE_TEXT, false, NULL, 0, NULL, '{"title":"Url","placeholder":"Url"}' ],
			[ $config->id, 'port', 'Port', FormField::TYPE_TEXT, false, NULL, 0, NULL, '{"title":"Port","placeholder":"Port"}' ],
			[ $config->id, 'resource_path', 'Resource Path', FormField::TYPE_TEXT, false, NULL, 0, NULL, '{"title":"Resource Path","placeholder":"Resource Path"}' ],
		];

        $this->batchInsert( $this->prefix . 'core_form_field', $columns, $fields );
    }

    private function insertDefaultConfig() {

        $columns = [ 'modelId', 'name', 'label', 'type', 'active', 'valueType', 'value', 'data' ];

        $metas	= [
            [ $this->site->id, 'active', 'Active', 'elasticsearch', 1, 'flag', '0', NULL ],
			[ $this->site->id, 'resource', 'Resource', 'elasticsearch', 1, 'flag', '0', NULL ],
			[ $this->site->id, 'url', 'Url', 'elasticsearch', 1, 'text', NULL, NULL ],
			[ $this->site->id, 'port', 'Port', 'elasticsearch', 1, 'text', NULL, NULL ],
			[ $this->site->id, 'resource_path', 'Resource Path', 'elasticsearch', 1, 'text', NULL, NULL ],
        ];

        $this->batchInsert( $this->prefix . 'core_site_meta', $columns, $metas );
    }

    public function down() {

        echo "m160822_061228_elasticsearch will be deleted with m160621_014408_core.\n";

        return true;
    }
}
