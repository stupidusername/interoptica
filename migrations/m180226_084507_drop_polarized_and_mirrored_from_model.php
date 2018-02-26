<?php

use yii\db\Migration;

/**
 * Class m180226_084507_drop_polarized_and_mirrored_from_model
 */
class m180226_084507_drop_polarized_and_mirrored_from_model extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
      $this->dropColumn('{{%model}}', 'polarized');
      $this->dropColumn('{{%model}}', 'mirrored');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
      $this->addColumn('{{%model}}', 'polarized', $this->boolean() . ' AFTER flex');
      $this->addColumn('{{%model}}', 'mirrored', $this->boolean() . ' AFTER polarized');
    }
}
