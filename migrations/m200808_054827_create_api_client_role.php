<?php

use dektrium\rbac\migrations\Migration;

class m200808_054827_create_api_client_role extends Migration
{

	# Added just for compat with standard migrations
	public $db;
	public $compact;

	public function safeUp()
	{
		$this->createRole('api_client', 'API Client');
	}

	public function safeDown()
	{
		$this->removeItem('api_client');
	}
}
