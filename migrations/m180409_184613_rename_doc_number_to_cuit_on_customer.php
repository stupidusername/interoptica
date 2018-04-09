<?php

use yii\db\Migration;

/**
 * Class m180409_184613_rename_doc_number_to_cuit_on_customer
 */
class m180409_184613_rename_doc_number_to_cuit_on_customer extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
      $this->renameColumn('{{%customer}}', 'doc_number', 'cuit');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->renameColumn('{{%customer}}', 'cuit', 'doc_number');
    }
}
