<?php

use yii\db\Migration;

class m170608_132128_create_delivery_status extends Migration
{
	public function up()
	{
		$this->createTable('{{%delivery_status}}', [
			'id' => $this->primaryKey(),
			'delivery_id' => $this->integer(),
			'status' => $this->smallInteger(),
			'create_datetime' => $this->dateTime(),
		]);

		$this->addForeignKey('{{%fk_status_delivery}}', '{{%delivery_status}}', 'delivery_id', '{{%delivery}}', 'id');
	}

	public function down()
	{
		$this->dropTable('{{%delivery_status}}');
	}
}
