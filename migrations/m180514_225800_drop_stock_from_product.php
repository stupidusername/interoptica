<?php

use yii\db\Migration;

/**
 * Class m180514_225800_drop_stock_from_product
 */
class m180514_225800_drop_stock_from_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->dropColumn('{{%product}}', 'stock');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->addColumn('{{%product}}', 'stock', $this->integer() . ' AFTER price');
    }
}
