<?php

use yii\db\Migration;

class m170207_054926_add_status_to_order extends Migration
{
    public function up()
    {
		$this->addColumn('{{%order}}', 'status', $this->smallInteger());
    }

    public function down()
    {
        $this->dropColumn('{{%order}}', 'status');
    }
}
