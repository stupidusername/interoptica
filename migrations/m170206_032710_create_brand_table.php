<?php

use yii\db\Migration;

/**
 * Handles the creation of table `brand`.
 */
class m170206_032710_create_brand_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%brand}}', [
            'id' => $this->primaryKey(),
			'gecom_code' => $this->string(),
			'name' => $this->string(),
        ]);
		
		$this->createIndex('{{%brand_unique_gecom_code}}', '{{%brand}}', 'gecom_code', true);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%brand}}');
    }
}
