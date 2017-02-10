<?php

use yii\db\Migration;

/**
 * Handles the creation of table `zone`.
 */
class m170209_235425_create_zone_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%zone}}', [
            'id' => $this->primaryKey(),
			'gecom_id' => $this->integer(),
			'name' => $this->string(),
			'deleted' => $this->boolean(),
        ]);
		
		$this->createIndex('{{%zone_unique_gecom_id}}', '{{%zone}}', 'gecom_id', true);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%zone}}');
    }
}
