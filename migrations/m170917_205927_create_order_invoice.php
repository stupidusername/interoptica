<?php

use yii\db\Migration;

class m170917_205927_create_order_invoice extends Migration
{
	public function safeUp()
	{
		$this->createTable('{{%order_invoice}}', [
			'id' => $this->primaryKey(),
			'order_id' => $this->integer(),
			'number' => $this->string(),
		]);

		$this->addForeignKey('{{%fk_order_invoice}}', '{{%order_invoice}}', 'order_id', '{{%order}}', 'id');
	}

	public function safeDown()
	{
		$this->dropTable('{{%order_invoice}}');
	}
}
