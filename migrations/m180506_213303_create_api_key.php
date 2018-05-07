<?php

use yii\db\Migration;

/**
 * Class m180506_213303_create_api_key
 */
class m180506_213303_create_api_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->createTable('{{%api_key}}', [
        'id' => $this->primaryKey(),
        'key' => $this->string(),
      ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%api_key}}');
    }
}
