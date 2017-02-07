<?php

use yii\db\Migration;

/**
 * Handles the creation of table `order`.
 */
class m170206_033128_create_order_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%order}}', [
            'id' => $this->primaryKey(),
			'user_id' => $this->integer(),
			'customer_id' => $this->integer(),
        ]);
		
		$this->addForeignKey('{{%fk_order_user}}', '{{%order}}', 'user_id', '{{%user}}', 'id');
		$this->addForeignKey('{{%fk_order_customer}}', '{{%order}}', 'customer_id', '{{%customer}}', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%order}}');
    }
}
