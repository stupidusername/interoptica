<?php

use yii\db\Migration;

class m170711_180754_monthly_summary extends Migration
{
	public function safeUp()
	{
		$this->createTable('{{%monthly_summary}}', [
			'id' => $this->primaryKey(),
			'user_id' => $this->integer(),
			'begin_date' => $this->date(),
			'invoiced' => $this->money(),
			'objective' => $this->integer(),
		]);

		$this->addForeignKey('{{%fk_monthly_summary}}', '{{%monthly_summary}}', 'user_id', '{{%user}}', 'id');
	}

	public function safeDown()
	{
		$this->dropTable('{{%monthly_summary}}');
	}
}
