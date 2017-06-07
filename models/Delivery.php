<?php

namespace app\models;

use codeonyii\yii2validators\AtLeastValidator;
use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "delivery".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $order_id
 * @property integer $issue_id
 * @property string $transport
 * @property integer $deleted
 *
 * @property User $user
 * @property Issue $issue
 * @property Order $order
 * @property integer $type
 * @property string $typeLabel
 */
class Delivery extends \yii\db\ActiveRecord
{
	const TYPE_ORDER = 0;
	const TYPE_ISSUE = 1;

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'delivery';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['order_id', 'issue_id', 'deleted'], 'integer'],
			[['transport'], 'string', 'max' => 255],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::className(), 'targetAttribute' => ['issue_id' => 'id']],
			[['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
			[['order_id', 'issue_id'], AtLeastValidator::className(), 'in' => ['order_id', 'issue_id']],
			[['order_id', 'issue_id'], 'unique'],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert) {
		if (parent::beforeSave($insert)) {
			if ($insert) {
				$this->user_id = Yii::$app->user->id;
			}
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'user_id' => 'ID Usuario',
			'order_id' => 'ID Pedido',
			'issue_id' => 'ID Reclamo',
			'transport' => 'Transporte',
		];
	}

	/**
	 * @return string[]
	 */
	public static function typeLabels() {
		return [
			self::TYPE_ORDER => 'Pedido',
			self::TYPE_ISSUE => 'Reclamo',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser() {
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssue()
	{
		return $this->hasOne(Issue::className(), ['id' => 'issue_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getOrder()
	{
		return $this->hasOne(Order::className(), ['id' => 'order_id']);
	}

	/**
	 * @return integer
	 */
	public function getType() {
		if ($this->order_id !== null xor $this->issue_id !== null) {
			return $this->order_id !== null ? self::TYPE_ORDER : TYPE_ISSUE;
		} else {
			throw new Exception('Delivery::order_id and Delivery::issue_id cannot be used at the same time.');
		}
	}

	/**
	 * @return string
	 */
	public function getTypeLabel() {
		return self::typeLabels()[$this->type];
	}
}
