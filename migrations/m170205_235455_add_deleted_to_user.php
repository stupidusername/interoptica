<?php

use yii\db\Migration;

class m170205_235455_add_deleted_to_user extends Migration
{
    public function up()
    {
		$this->addColumn('{{%user}}', 'deleted', $this->boolean());
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'deleted');
    }
}
