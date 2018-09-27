<?php

use yii\db\Migration;

/**
 * Class m180927_175858_add_available_to_product
 */
class m180927_175858_add_available_to_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->addColumn('{{%product}}', 'available', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'available');
    }
}
