<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "monthly_summary".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $begin_date
 * @property string $invoiced
 * @property integer $objective
 *
 * @property User $user
 */
class MonthlySummary extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'monthly_summary';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'objective'], 'integer'],
            [['begin_date'], 'date'],
            [['invoiced'], 'number'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
	    [['user_id', 'begin_date'], 'unique', 'targetAttribute' => ['user_id', 'begin_date']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'ID Usuario',
            'begin_date' => 'Fecha de Inicio',
            'invoiced' => 'Facturado',
            'objective' => 'Objetivo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
