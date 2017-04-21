<?php

use yii\db\Migration;

class m170421_221646_create_fail extends Migration
{
    public function up()
    {
		$this->createTable('{{%fail}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string(),
			'deleted' => $this->boolean(),
		]);
    }

    public function down()
    {
		$this->dropTable('{{%fail}}');
    }
}
