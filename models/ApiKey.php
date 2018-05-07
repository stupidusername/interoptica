<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "api_key".
 *
 * @property int $id
 * @property string $key
 */
class ApiKey extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_key';
    }
}
