<?php

use yii\db\Migration;

class m170426_212957_remove_comment_from_issue extends Migration
{
    public function up()
    {
		$this->dropColumn('{{%issue}}', 'comment');
    }

    public function down()
    {
        $this->addColumn('{{%issue}}', 'comment', $this->text() . ' AFTER issue_type_id');
    }
}
