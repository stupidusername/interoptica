<?php

use yii\db\Migration;

class m170218_201637_add_province_to_customer extends Migration
{
    public function up()
    {
		$this->addColumn('{{%customer}}', 'province', $this->string() . ' AFTER zip_code');
    }

    public function down()
    {
        $this->dropColumn('{{%customer}}', 'province');
    }
}
