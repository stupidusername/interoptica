<?php

use yii\db\Migration;

/**
 * Class m171221_045116_alter_model
 */
class m171221_045116_alter_model extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
      $this->dropColumn('{{%model}}', 'gecom_code');
      $this->addColumn('{{%model}}', 'type', $this->smallInteger() . ' AFTER id');
      $this->alterColumn('{{%model}}', 'name', $this->string() . ' AFTER type');
      $this->addColumn('{{%model}}', 'description', $this->text() . ' AFTER name');
      $this->addColumn('{{%model}}', 'front_size', $this->smallInteger()  . ' AFTER description');
      $this->addColumn('{{%model}}', 'lens_width', $this->smallInteger()  . ' AFTER front_size');
      $this->addColumn('{{%model}}', 'bridge_size', $this->smallInteger() . ' AFTER lens_width');
      $this->addColumn('{{%model}}', 'temple_length', $this->smallInteger() . ' AFTER bridge_size');
      $this->addColumn('{{%model}}', 'base', $this->smallInteger() . ' AFTER temple_length');
      $this->addColumn('{{%model}}', 'flex', $this->boolean() . ' AFTER base');
      $this->addColumn('{{%model}}', 'polarized', $this->boolean() . ' AFTER flex');
      $this->addColumn('{{%model}}', 'mirrored', $this->boolean() . ' AFTER polarized');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
      $this->dropColumn('{{%model}}', 'description');
      $this->dropColumn('{{%model}}', 'front_size');
      $this->dropColumn('{{%model}}', 'lens_width');
      $this->dropColumn('{{%model}}', 'bridge_size');
      $this->dropColumn('{{%model}}', 'temple_length');
      $this->dropColumn('{{%model}}', 'base');
      $this->dropColumn('{{%model}}', 'flex');
      $this->dropColumn('{{%model}}', 'polarized');
      $this->dropColumn('{{%model}}', 'mirrored');
      $this->dropColumn('{{%model}}', 'type');
      $this->addColumn('{{%model}}', 'gecom_code', $this->string() . ' AFTER id');
      $this->alterColumn('{{%model}}', 'name', $this->string() . ' AFTER gecom_code');
    }
}
