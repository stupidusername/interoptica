<?php

use yii\db\Migration;

class m170414_061932_create_issue_type extends Migration
{
    public function up()
    {
		$this->createTable('{{%issue_type}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string(),
			'deleted' => $this->boolean(),
		]);
    }

    public function down()
    {
        $this->dropTable('{{%issue_type}}');
    }
}
