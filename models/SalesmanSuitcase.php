<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "salesman_suitcase".
 *
 * @property int $id
 * @property int $user_id
 * @property int $customer_id
 * @property int $suitcase_id
 *
 * @property Customer $customer
 * @property Suitcase $suitcase
 * @property User $user
 */
class SalesmanSuitcase extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'salesman_suitcase';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'customer_id', 'suitcase_id'], 'integer'],
            [['user_id', 'customer_id', 'suitcase_id'], 'required'],
            [['suitcase_id'], 'targetAttribute' => ['customer_id', 'suitcase_id'], 'message' => 'Esta combinación ya ha sido asignada.'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['suitcase_id'], 'exist', 'skipOnError' => true, 'targetClass' => Suitcase::className(), 'targetAttribute' => ['suitcase_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'ID Usuario',
            'customer_id' => 'ID Cliente',
            'suitcase_id' => 'ID Valija',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSuitcase()
    {
        return $this->hasOne(Suitcase::className(), ['id' => 'suitcase_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
