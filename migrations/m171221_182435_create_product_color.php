<?php

use yii\db\Migration;

/**
 * Class m171221_182435_create_product_color
 */
class m171221_182435_create_product_color extends Migration
{
    public function safeUp()
    {
      $this->createTable('{{%product_color}}', [
        'product_id' => $this->integer(),
        'color_id' => $this->integer(),
      ]);

      $this->addForeignKey('{{%fk_product_color_product}}', '{{%product_color}}', 'product_id', '{{%product}}', 'id');
      $this->addForeignKey('{{%fk_product_color_color}}', '{{%product_color}}', 'color_id', '{{%color}}', 'id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
      $this->dropTable('{{%product_color}}');
    }
}
