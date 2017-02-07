<?php

use yii\db\Migration;

/**
 * Handles the creation of table `product`.
 */
class m170206_032838_create_product_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%product}}', [
            'id' => $this->primaryKey(),
			'variant_id' => $this->integer(),
			'gecom_code' => $this->string(),
			'gecom_desc' => $this->string(),
			'price' => $this->money(),
			'stock' => $this->integer(),
        ]);
		
		$this->createIndex('{{%product_unique_gecom_code}}', '{{%product}}', 'gecom_code', true);
		$this->createIndex('{{%product_unique_gecom_desc}}', '{{%product}}', 'gecom_desc', true);
		$this->addForeignKey('{{%fk_product_variant}}', '{{%product}}', 'variant_id', '{{%variant}}', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%product}}');
    }
}
