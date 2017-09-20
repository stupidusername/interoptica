<?php

use yii\db\Migration;

class m170920_182744_add_extra_to_product extends Migration
{
	public function safeUp()
	{
		$this->addColumn('{{%product}}', 'extra', $this->boolean() . ' AFTER stock');
	}

	public function safeDown()
	{
		$this->dropColumn('{{%product}}', 'extra');
	}
}
