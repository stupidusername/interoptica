<?php

use yii\db\Migration;

class m170927_051958_alter_delivery extends Migration
{
	public function safeUp()
	{
		$this->dropColumn('{{%delivery}}', 'transport');
		$this->addColumn('{{%delivery}}', 'transport_id', $this->integer() . ' AFTER user_id');
		$this->addForeignKey('{{%fk_delivery_transport}}', '{{%delivery}}', 'transport_id', '{{%transport}}', 'id');
	}

	public function safeDown()
	{
		$this->dropForeignKey('{{%fk_delivery_transport}}', '{{%delivery}}');
		$this->dropColumn('{{%delivery}}', 'transport_id');
		$this->addColumn('{{%delivery}}', 'transport', $this->string() . ' AFTER user_id');
	}
}
