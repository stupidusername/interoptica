<?php

use yii\db\Migration;

class m170605_165302_add_tax_situation_category_to_customer extends Migration
{
	public function up()
	{
		$this->addColumn('{{%customer}}', 'tax_situation_category', $this->string() . ' AFTER tax_situation');
	}

	public function down()
	{
		$this->dropColumn('{{%customer}}', 'tax_situation_category');
	}
}
