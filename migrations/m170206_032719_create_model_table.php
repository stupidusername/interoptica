<?php

use yii\db\Migration;

/**
 * Handles the creation of table `model`.
 */
class m170206_032719_create_model_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%model}}', [
            'id' => $this->primaryKey(),
			'gecom_code' => $this->string(),
			'name' => $this->string(),
			'brand_id' => $this->integer(),
        ]);
		
		$this->createIndex('{{%model_unique_gecom_code}}', '{{%model}}', 'gecom_code', true);
		$this->addForeignKey('{{%fk_model_brand}}', '{{%model}}', 'brand_id', '{{%brand}}', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%model}}');
    }
}
