<?php

use app\models\OrderStatus;
use yii\db\Migration;

class m170608_051708_update_order_status extends Migration
{
	public function up()
	{
		OrderStatus::updateAllCounters(['status' => 2], ['>=', 'status', OrderStatus::STATUS_COLLECT_REVISION]);
		OrderStatus::updateAllCounters(['status' => 1], ['>=', 'status', OrderStatus::STATUS_PUT_TOGETHER_PRINTED]);
		OrderStatus::updateAllCounters(['status' => 1], ['>=', 'status', OrderStatus::STATUS_WAITING_FOR_TRANSPORT]);
	}

	public function down()
	{
		return false;
	}
}
