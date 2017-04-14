<?php

use yii\db\Migration;

class m170414_061936_create_issue_status extends Migration
{
    public function up()
    {
		$this->createTable('{{%issue_status}}', [
			'id' => $this->primaryKey(),
			'issue_id' => $this->integer(),
			'status' => $this->smallInteger(),
			'create_datetime' => $this->dateTime(),
		]);
		
		$this->addForeignKey('{{%fk_status_issue}}', '{{%issue_status}}', 'issue_id', '{{%issue}}', 'id');
    }

    public function down()
    {
        $this->dropTable('{{%issue_status}}');
    }
}
