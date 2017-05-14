<?php

use app\models\IssueStatus;
use yii\db\Migration;

class m170514_192708_update_issue_status extends Migration
{
    public function up()
    {
		IssueStatus::updateAllCounters(['status' => 1], ['>=', 'status', IssueStatus::STATUS_OPEN_URGENT]);
		IssueStatus::updateAllCounters(['status' => 2], ['>=', 'status', IssueStatus::STATUS_PENDING_RESPONSE]);
    }

    public function down()
    {
        return false;
    }
}
