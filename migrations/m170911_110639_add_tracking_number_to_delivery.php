<?php

use yii\db\Migration;

class m170911_110639_add_tracking_number_to_delivery extends Migration
{
	public function safeUp()
	{
		$this->addColumn('{{%delivery}}', 'tracking_number', $this->string() . ' AFTER transport');
	}

	public function safeDown()
	{
		$this->dropColumn('{{%delivery}}', 'tracking_number');
	}
}
