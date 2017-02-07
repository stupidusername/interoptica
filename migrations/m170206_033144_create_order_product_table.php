<?php

use yii\db\Migration;

/**
 * Handles the creation of table `order_product`.
 */
class m170206_033144_create_order_product_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%order_product}}', [
			'order_id' => $this->integer(),
			'product_id' => $this->integer(),
        ]);
		
		$this->addPrimaryKey('{{%pk_order_product}}', '{{%order_product}}', ['order_id', 'product_id']);
		$this->addForeignKey('{{%fk_order_product}}', '{{%order_product}}', 'order_id', 'order', 'id');
		$this->addForeignKey('{{%fk_product_order}}', '{{%order_product}}', 'product_id', 'product', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%order_product}}');
    }
}
