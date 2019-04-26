<?php

namespace app\models;

use app\components\DeletedQuery;
use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "order_condition".
 *
 * @property int $id
 * @property string $title
 * @property string $interest_rate_percentage
 * @property int $editable_in_order
 * @property int $deleted
 *
 * @property Order[] $orders
 */
class OrderCondition extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'order_condition';
    }

    /**
     * {@inheritdoc}
     */
  	public function behaviors() {
  		return [
  			'softDeleteBehavior' => [
  				'class' => SoftDeleteBehavior::className(),
  				'softDeleteAttributeValues' => [
  					'deleted' => true,
  				],
  				'replaceRegularDelete' => true,
  			],
  		];
  	}

    /**
     * {@inheritdoc}
     */
    public static function find() {
      return new DeletedQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['title'], 'required'],
            [['interest_rate_percentage'], 'required', 'when' => function ($model) { return !((bool) $model->editable_in_order); }],
            [['interest_rate_percentage'], 'number'],
            [['editable_in_order', 'deleted'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'title' => 'Título',
            'interest_rate_percentage' => 'Porcetaje de Interés',
            'editable_in_order' => 'Editable en Pedido',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders() {
        return $this->hasMany(Order::className(), ['order_condition_id' => 'id']);
    }
}
