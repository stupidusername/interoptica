<?php

use yii\db\Migration;

class m170204_034351_add_gecom_id_to_user extends Migration
{
    public function up()
    {
		$this->addColumn('{{%user}}', 'gecom_id', $this->integer() . ' AFTER email');
		
		$this->createIndex('{{%user_unique_gecom_id}}', '{{%user}}', 'gecom_id', true);
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'gecom_id');
    }
}
