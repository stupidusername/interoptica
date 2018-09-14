<?php

use dektrium\rbac\migrations\Migration;

class m180914_191525_create_editor_role extends Migration
{

	# Added just for compat with standard migrations
	public $db;
	public $compact;

	public function safeUp()
	{
		$this->createRole('editor', 'Edición');
	}

	public function safeDown()
	{
		$this->removeItem('editor');
	}
}
