<?php

use app\models\OrderStatus;
use yii\db\Migration;

/**
 * Class m180923_192738_update_order_status
 */
class m180923_192738_update_order_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      OrderStatus::updateAllCounters(['status' => 1]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      OrderStatus::updateAllCounters(['status' => -1]);
    }
}
