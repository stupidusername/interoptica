<?php

use yii\db\Migration;

class m170612_064207_add_user_id_to_delivery_status extends Migration
{
	public function up()
	{
		$this->addColumn('{{%delivery_status}}', 'user_id', $this->integer());
		$this->addForeignKey('{{%fk_delivery_status_user}}', '{{%delivery_status}}', 'user_id', '{{%user}}', 'id');
	}

	public function down()
	{
		$this->dropForeignKey('{{%fk_delivery_status_user}}', '{{%delivery_status}}');
		$this->dropColumn('{{%delivery_status}}', 'user_id');
	}
}
