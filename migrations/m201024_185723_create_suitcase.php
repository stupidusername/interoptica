<?php

use yii\db\Schema;
use yii\db\Migration;

class m201024_185723_create_suitcase extends Migration
{
    public function up() {
		$this->createTable('{{%suitcase}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'deleted' => $this->boolean(),
        ]);
	}

	public function down() {
		$this->dropTable('{{%suitcase}}');
	}
}
