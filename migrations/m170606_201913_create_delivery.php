<?php

use yii\db\Migration;

class m170606_201913_create_delivery extends Migration
{
	public function up()
	{
		$this->createTable('{{%delivery}}', [
			'id' => $this->primaryKey(),
			'order_id' => $this->integer(),
			'issue_id' => $this->integer(),
			'transport' => $this->string(),
			'deleted' => $this->boolean(),
		]);

		$this->addForeignKey('{{%fk_delivery_order}}', '{{%delivery}}', 'order_id', '{{%order}}', 'id');
		$this->addForeignKey('{{%fk_delivery_issue}}', '{{%delivery}}', 'issue_id', '{{%issue}}', 'id');
	}

	public function down()
	{
		$this->dropTable('{{%delivery}}');
	}
}
