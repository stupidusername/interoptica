<?php

use yii\db\Migration;

/**
 * Class m190426_040848_add_condition_to_order
 */
class m190426_040848_add_condition_to_order extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('{{%order}}', 'order_condition_id', $this->integer() . ' AFTER discount_percentage');
        $this->addColumn('{{%order}}', 'interest_rate_percentage', $this->money() . ' AFTER order_condition_id');

        $this->addForeignKey(
            '{{%fk_order_order_condition}}',
            '{{%order}}',
            'order_condition_id',
            '{{%order_condition}}',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropForeignKey('{{%fk_order_order_condition}}', '{{%order}}');
        $this->dropColumn('{{%order}}', 'order_condition_id');
        $this->dropColumn('{{%order}}', 'interest_rate_percentage');
    }
}
