<?php

use yii\db\Schema;
use yii\db\Migration;

class m201024_205633_create_suitcase_brand extends Migration
{
    public function up() {
		$this->createTable('{{%suitcase_brand}}', [
            'id' => $this->primaryKey(),
            'suitcase_id' => $this->integer(),
            'brand_id' => $this->integer(),
        ]);

        $this->addForeignKey('{{%fk_suitcase_brand_suitcase}}', '{{%suitcase_brand}}', 'suitcase_id', '{{%suitcase}}', 'id');
        $this->addForeignKey('{{%fk_suitcase_brand_brand}}', '{{%suitcase_brand}}', 'brand_id', '{{%brand}}', 'id');
	}

	public function down() {
        $this->dropForeignKey('{{%fk_suitcase_brand_suitcase}}', '{{%suitcase_brand}}');
        $this->dropForeignKey('{{%fk_suitcase_brand_brand}}', '{{%suitcase_brand}}');
		$this->dropTable('{{%suitcase_brand}}');
	}
}
