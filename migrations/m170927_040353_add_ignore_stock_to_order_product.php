<?php

use yii\db\Migration;

class m170927_040353_add_ignore_stock_to_order_product extends Migration
{
	public function safeUp()
	{
		$this->addColumn('{{%order_product}}', 'ignore_stock', $this->boolean());

	}

	public function safeDown()
	{
		$this->dropColumn('{{%order_product}}', 'ignore_stock');
	}
}
