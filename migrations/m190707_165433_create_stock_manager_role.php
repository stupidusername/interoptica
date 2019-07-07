<?php

use dektrium\rbac\migrations\Migration;

class m190707_165433_create_stock_manager_role extends Migration
{

	# Added just for compat with standard migrations
	public $db;
	public $compact;

	public function safeUp()
	{
		$this->createRole('stock_manager', 'Stock Manager');
	}

	public function safeDown()
	{
		$this->removeItem('stock_manager');
	}
}
