<?php

use yii\db\Migration;

class m170426_214101_create_issue_comment extends Migration
{
    public function up()
    {
		$this->createTable('{{%issue_comment}}', [
			'id' => $this->primaryKey(),
			'issue_id' => $this->integer(),
			'user_id' => $this->integer(),
			'create_datetime' => $this->dateTime(),
			'comment' => $this->text(),
		]);
		
		$this->addForeignKey('{{%fk_issue_comment_issue}}', '{{%issue_comment}}', 'issue_id', '{{%issue}}', 'id');
		$this->addForeignKey('{{%fk_issue_comment_user}}', '{{%issue_comment}}', 'user_id', '{{%user}}', 'id');
    }

    public function down()
    {
        $this->dropTable('{{%issue_comment}}');
    }
}
