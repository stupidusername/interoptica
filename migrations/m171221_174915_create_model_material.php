<?php

use yii\db\Migration;

/**
 * Class m171221_174915_create_model_material
 */
class m171221_174915_create_model_material extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
      $this->createTable('{{%model_material}}', [
        'model_id' => $this->integer(),
        'material_id' => $this->integer(),
      ]);

      $this->addForeignKey('{{%fk_model_material_model}}', '{{%model_material}}', 'model_id', '{{%model}}', 'id');
      $this->addForeignKey('{{%fk_model_material_material}}', '{{%model_material}}', 'material_id', '{{%material}}', 'id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
      $this->dropTable('{{%model_material}}');
    }
}
