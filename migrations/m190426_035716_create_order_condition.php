<?php

use yii\db\Migration;

/**
 * Class m190426_035716_create_order_condition
 */
class m190426_035716_create_order_condition extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('{{%order_condition}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(),
            'interest_rate_percentage' => $this->money(),
            'editable_in_order' => $this->boolean(),
            'deleted' => $this->boolean(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable('{{%order_condition}}');
    }
}
