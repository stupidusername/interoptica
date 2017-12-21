<?php

use yii\db\Migration;

/**
 * Class m171221_225743_alter_collection
 */
class m171221_225743_alter_collection extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
      $this->addColumn('{{%collection}}', 'deleted', $this->boolean());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%collection}}', 'deleted');
    }
}
