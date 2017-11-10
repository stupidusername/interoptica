<?php

use yii\db\Migration;

class m171110_165433_add_comment_to_issue_invoice extends Migration
{
    public function safeUp()
    {
	    $this->addColumn('{{%issue_invoice}}', 'comment', $this->text());
    }

    public function safeDown()
    {
	    $this->dropColumn('{{%issue_invoice}}', 'comment');
    }
}
