<?php

use app\models\OrderStatus;
use yii\db\Migration;

class m170514_183206_update_order_status extends Migration
{
    public function up()
    {
		OrderStatus::updateAllCounters(['status' => 1], ['>=', 'status', OrderStatus::STATUS_PENDING_PUT_TOGETHER]);
    }

    public function down()
    {
        return false;
    }
}
