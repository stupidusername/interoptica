<?php

use yii\db\Migration;

/**
 * Class m171221_174605_create_material
 */
class m171221_174605_create_material extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
      $this->createTable('{{%material}}', [
        'id' => $this->primaryKey(),
        'frequency' => $this->integer(),
        'name' => $this->string(),
      ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
      $this->dropTable('{{%material}}');
    }
}
