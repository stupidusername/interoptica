<?php

use yii\db\Migration;

class m170917_205942_create_issue_invoice extends Migration
{
	public function safeUp()
	{
		$this->createTable('{{%issue_invoice}}', [
			'id' => $this->primaryKey(),
			'issue_id' => $this->integer(),
			'number' => $this->string(),
		]);

		$this->addForeignKey('{{%fk_issue_invoice}}', '{{%issue_invoice}}', 'issue_id', '{{%issue}}', 'id');
	}

	public function safeDown()
	{
		$this->dropTable('{{%issue_invoice}}');
	}
}
