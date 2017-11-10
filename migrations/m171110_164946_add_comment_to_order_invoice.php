<?php

use yii\db\Migration;

class m171110_164946_add_comment_to_order_invoice extends Migration
{
    public function safeUp()
    {
	    $this->addColumn('{{%order_invoice}}', 'comment', $this->text());
    }

    public function safeDown()
    {
	    $this->dropColumn('{{%order_invoice}}', 'comment');
    }
}
