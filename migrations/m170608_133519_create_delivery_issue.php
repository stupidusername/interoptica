<?php

use yii\db\Migration;

class m170608_133519_create_delivery_issue extends Migration
{
	public function up()
	{
		$this->createTable('{{%delivery_issue}}', [
			'id' => $this->primaryKey(),
			'delivery_id' => $this->integer(),
			'issue_id' => $this->integer(),
		]);

		$this->addForeignKey('{{%fk_delivery_issue_delivery}}', '{{%delivery_issue}}', 'delivery_id', '{{%delivery}}', 'id');
		$this->addForeignKey('{{%fk_delivery_issue_issue}}', '{{%delivery_issue}}', 'issue_id', '{{%issue}}', 'id');
	}

	public function down()
	{
		$this->dropTable('{{%delivery_issue}}');
	}
}
