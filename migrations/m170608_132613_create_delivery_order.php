<?php

use yii\db\Migration;

class m170608_132613_create_delivery_order extends Migration
{
	public function up()
	{
		$this->createTable('{{%delivery_order}}', [
			'id' => $this->primaryKey(),
			'delivery_id' => $this->integer(),
			'order_id' => $this->integer(),
		]);

		$this->addForeignKey('{{%fk_delivery_order_delivery}}', '{{%delivery_order}}', 'delivery_id', '{{%delivery}}', 'id');
		$this->addForeignKey('{{%fk_delivery_order_order}}', '{{%delivery_order}}', 'order_id', '{{%order}}', 'id');
	}

	public function down()
	{
		$this->dropTable('{{%delivery_order}}');
	}
}
