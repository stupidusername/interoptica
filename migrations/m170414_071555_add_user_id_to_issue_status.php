<?php

use yii\db\Migration;

class m170414_071555_add_user_id_to_issue_status extends Migration
{
    public function up()
    {
		$this->addColumn('{{%issue_status}}', 'user_id', $this->integer());
		$this->addForeignKey('{{%fk_issue_status_user}}', '{{%issue_status}}', 'user_id', '{{%user}}', 'id');
    }

    public function down()
    {
		$this->dropForeignKey('{{%fk_issue_status_user}}', '{{%issue_status}}');
		$this->dropColumn('{{%issue_status}}', 'user_id');
    }
}
