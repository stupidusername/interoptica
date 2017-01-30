<?php

use yii\db\Migration;

class m170130_003504_create_customer extends Migration
{
    public function up()
    {
		$this->createTable('{{%customer}}', [
			'id' => $this->primaryKey(),
			'gecom_id' => $this->integer(),
			'name' => $this->string(),
			'tax_situation' => $this->string(),
			'address' => $this->string(),
			'zip_code' => $this->string(),
			'locality' => $this->string(),
			'phone_number' => $this->string(),
			'doc_number' => $this->string(),
		]);
    }

    public function down()
    {
        $this->dropTable('{{%customer}}');
    }
}
