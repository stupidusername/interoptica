<?php

use yii\db\Migration;

class m170722_155600_add_email_to_customer extends Migration
{
	public function safeUp()
	{
		$this->addColumn('{{%customer}}', 'email', $this->string() . ' AFTER name');
	}

	public function safeDown()
	{
		$this->dropColumn('{{%customer}}', 'email');
	}
}
