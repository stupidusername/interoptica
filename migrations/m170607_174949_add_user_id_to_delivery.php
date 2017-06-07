<?php

use yii\db\Migration;

class m170607_174949_add_user_id_to_delivery extends Migration
{
	public function up()
	{
		$this->addColumn('{{%delivery}}', 'user_id', $this->integer() . ' AFTER id');
		$this->addForeignKey('{{%fk_delivery_user}}', '{{%delivery}}', 'user_id', '{{%user}}', 'id');
	}

	public function down()
	{
		$this->dropForeignKey('{{%fk_delivery_user}}', '{{%delivery}}');
		$this->dropColumn('{{%delivery}}', 'user_id');
	}
}
