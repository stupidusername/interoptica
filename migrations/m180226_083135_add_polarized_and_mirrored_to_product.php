<?php

use yii\db\Migration;

/**
 * Class m180226_083135_add_polarized_and_mirrored_to_product
 */
class m180226_083135_add_polarized_and_mirrored_to_product extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
      $this->addColumn('{{%product}}', 'polarized', $this->boolean() . ' AFTER code');
      $this->addColumn('{{%product}}', 'mirrored', $this->boolean() . ' AFTER polarized');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
      $this->dropColumn('{{%product}}', 'polarized');
      $this->dropColumn('{{%product}}', 'mirrored');
    }
}
