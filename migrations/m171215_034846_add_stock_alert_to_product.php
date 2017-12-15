<?php

use yii\db\Migration;

/**
 * Class m171215_034846_add_stock_alert_to_product
 */
class m171215_034846_add_stock_alert_to_product extends Migration
{
    public function safeUp()
    {
      $this->addColumn('{{%product}}', 'running_low', $this->boolean() . ' AFTER stock');
      $this->addColumn('{{%product}}', 'running_low_date', $this->date() . ' AFTER running_low');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
      $this->dropColumn('{{%product}}', 'running_low');
      $this->dropColumn('{{%product}}', 'running_low_date');
    }
}
