<?php

use yii\db\Migration;

class m170210_042553_add_deleted_to_tables extends Migration
{
    public function up()
    {
		$this->addColumn('{{%brand}}', 'deleted', $this->boolean());
		$this->addColumn('{{%model}}', 'deleted', $this->boolean());
		$this->addColumn('{{%variant}}', 'deleted', $this->boolean());
		$this->addColumn('{{%product}}', 'deleted', $this->boolean());
		$this->addColumn('{{%order}}', 'deleted', $this->boolean());
    }

    public function down()
    {
        $this->dropColumn('{{%order}}', 'deleted');
		$this->dropColumn('{{%product}}', 'deleted');
		$this->dropColumn('{{%variant}}', 'deleted');
		$this->dropColumn('{{%model}}', 'deleted');
		$this->dropColumn('{{%brand}}', 'deleted');
    }
}
