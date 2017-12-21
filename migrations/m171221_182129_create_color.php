<?php

use yii\db\Migration;

/**
 * Class m171221_182129_create_color
 */
class m171221_182129_create_color extends Migration
{
  /**
   * @inheritdoc
   */
  public function safeUp()
  {
    $this->createTable('{{%color}}', [
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
    $this->dropTable('{{%color}}');
  }
}
