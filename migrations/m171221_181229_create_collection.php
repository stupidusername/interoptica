<?php

use yii\db\Migration;

/**
 * Class m171221_181229_create_collection
 */
class m171221_181229_create_collection extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
      $this->createTable('{{%collection}}', [
        'id' => $this->primaryKey(),
        'name' => $this->string(),
      ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%collection}}');
    }
}
