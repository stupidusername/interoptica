<?php

use yii\db\Migration;

/**
 * Class m171221_182650_create_product_lens_color
 */
class m171221_182650_create_product_lens_color extends Migration
{
  public function safeUp()
  {
    $this->createTable('{{%product_lens_color}}', [
      'product_id' => $this->integer(),
      'color_id' => $this->integer(),
    ]);

    $this->addForeignKey('{{%fk_product_lens_color_product}}', '{{%product_lens_color}}', 'product_id', '{{%product}}', 'id');
    $this->addForeignKey('{{%fk_product_lens_color_color}}', '{{%product_lens_color}}', 'color_id', '{{%color}}', 'id');
  }

  /**
   * @inheritdoc
   */
  public function safeDown()
  {
    $this->dropTable('{{%product_lens_color}}');
  }
}
