<?php

use yii\db\Migration;

/**
 * Handles the creation of table `variant`.
 */
class m170206_032803_create_variant_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%variant}}', [
            'id' => $this->primaryKey(),
			'gecom_code' => $this->string(),
			'name' => $this->string(),
			'model_id' => $this->integer(),
        ]);
		
		$this->createIndex('{{%variant_unique_gecom_code}}', '{{%variant}}', 'gecom_code', true);
		$this->addForeignKey('{{%fk_variant_model}}', '{{%variant}}', 'model_id', '{{%model}}', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%variant}}');
    }
}
