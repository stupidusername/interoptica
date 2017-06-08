<?php

use yii\db\Migration;

class m170608_130134_alter_delivery_table extends Migration
{
	public function up()
	{
		$this->dropForeignKey('{{%fk_delivery_order}}', '{{%delivery}}');
		$this->dropForeignKey('{{%fk_delivery_issue}}', '{{%delivery}}');
		$this->dropColumn('{{%delivery}}', 'order_id');
		$this->dropColumn('{{%delivery}}', 'issue_id');
	}

	public function down()
	{
		$this->addColumn('{{%delivery}}', 'order_id', $this->integer() . ' AFTER user_id');
		$this->addColumn('{{%delivery}}', 'issue_id', $this->integer() . ' AFTER order_id');
		$this->addForeignKey('{{%fk_delivery_order}}', '{{%delivery}}', 'order_id', '{{%order}}', 'id');
		$this->addForeignKey('{{%fk_delivery_issue}}', '{{%delivery}}', 'issue_id', '{{%issue}}', 'id');
	}
}
