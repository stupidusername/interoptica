<?php

use yii\db\Migration;

class m170606_183648_create_transport extends Migration
{
	public function up()
	{
		$this->createTable('{{%transport}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string(),
			'deleted' => $this->boolean(),
		]);
	}

	public function down()
	{
		$this->dropTable('{{%transport}}');
	}
}
