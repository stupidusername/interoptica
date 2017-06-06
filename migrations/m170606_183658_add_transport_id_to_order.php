<?php

use yii\db\Migration;

class m170606_183658_add_transport_id_to_order extends Migration
{
	public function up()
	{
		$this->addColumn('{{%order}}', 'transport_id', $this->integer() . ' AFTER customer_id');
		$this->addForeignKey('{{%fk_order_transport}}', '{{%order}}', 'transport_id', '{{%transport}}', 'id');
	}

	public function down()
	{
		$this->dropForeignKey('{{fk_order_transport}}', '{{%order}}');
		$this->dropColumn('{{%order}}', 'transport_id');
	}
}
