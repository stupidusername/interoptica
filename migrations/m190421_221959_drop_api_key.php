<?php

use yii\db\Migration;

/**
 * Class m190421_221959_drop_api_key
 */
class m190421_221959_drop_api_key extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->dropTable('{{%api_key}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->createTable('{{%api_key}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(),
        ]);
    }
}
