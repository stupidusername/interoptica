<?php

use yii\db\Migration;

class m170205_235451_add_deleted_to_customer extends Migration
{
    public function up()
    {
		$this->addColumn('{{%customer}}', 'deleted', $this->boolean());
    }

    public function down()
    {
        $this->dropColumn('{{%customer}}', 'deleted');
    }
}
