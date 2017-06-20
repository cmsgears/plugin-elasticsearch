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

    private $uploadsDir;
    private $uploadsUrl;

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
            'siteId'	=> $this->site->id,
            'createdBy' => $this->master->id, 'modifiedBy' => $this->master->id,
            'name'		=> 'Config Elasticsearch', 'slug' => 'config-elasticsearch',
            'type'		=> CoreGlobal::TYPE_SYSTEM,
            'description' => 'Elasticsearch configuration form.',
            'successMessage' => 'All configurations saved successfully.',
            'captcha'	=> false,
            'visibility' => Form::VISIBILITY_PROTECTED,
            'active'	=> true, 'userMail' => false,'adminMail' => false,
            'createdAt' => DateUtil::getDateTime(),
            'modifiedAt' => DateUtil::getDateTime()
        ]);

        $config	= Form::findBySlug( 'config-elasticsearch', CoreGlobal::TYPE_SYSTEM );

        $columns = [ 'formId', 'name', 'label', 'type', 'compress', 'validators', 'order', 'icon', 'htmlOptions' ];

        $fields	= [
            [ $config->id, 'active', 'Active', FormField::TYPE_TOGGLE, false, 'required', 0, NULL, '{"title":"Active"}' ]
		];

        $this->batchInsert( $this->prefix . 'core_form_field', $columns, $fields );
    }

    private function insertDefaultConfig() {

        $columns = [ 'modelId', 'name', 'label', 'type', 'valueType', 'value' ];

        $metas	= [
            [ $this->site->id, 'Elasticsearch', 'Elasticsearch', 'elasticsearch', 'flag', '0' ]
        ];

        $this->batchInsert( $this->prefix . 'core_site_meta', $columns, $metas );
    }

    public function down() {

        echo "m160822_061228_elasticsearch will be deleted with m160621_014408_core.\n";

        return true;
    }
}
