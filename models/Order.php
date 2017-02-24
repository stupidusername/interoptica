<?php

namespace app\models;

use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $customer_id
 * @property string $discount_percentage
 * @property string $comment
 * @property integer $deleted
 *
 * @property Customer $customer
 * @property User $user
 * @property OrderProduct[] $orderProducts
 * @property Product[] $products
 * @property OrderStatus[] $orderStatuses
 * @property OrderStatus $orderStatus
 * @property OrderStatus $enteredOrderStatus
 */
class Order extends \yii\db\ActiveRecord
{
	const SCENARIO_UPDATE = 'update';
	
	/**
	 * The last order status saved
	 * @var integer 
	 */
	public $status;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }
	
	/** @inheritdoc */
	public function behaviors() {
		return [
			'softDeleteBehavior' => [
				'class' => SoftDeleteBehavior::className(),
				'softDeleteAttributeValues' => [
					'deleted' => true
				],
				'replaceRegularDelete' => true
			],
		];
	}
	
	/** @inheritdoc */
	public static function find()
    {
        return parent::find()->where(['or', ['deleted' => null], ['deleted' => 0]]);
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
	public function afterSave($insert, $changedAttributes) {
		if ($insert) {
			$this->status = OrderStatus::STATUS_ENTERED;
		}
		$oldStatus = $this->orderStatus;
		// Save order status if there was a change
		if (!$oldStatus || $oldStatus->status != $this->status) {
			$orderStatus = new OrderStatus();
			$orderStatus->order_id = $this->id;
			$orderStatus->status = $this->status;
			$orderStatus->create_datetime = gmdate('Y-m-d H:i:s');
			$orderStatus->save(false);
		}
		parent::afterSave($insert, $changedAttributes);
	}
	
	/**
	 * @inheritdoc
	 */
	public function afterFind() {
		$this->status = $this->orderStatus ? $this->orderStatus->status : null;
		parent::afterFind();
	}
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['customer_id'], 'integer'],
            [['discount_percentage'], 'number', 'min' => 0, 'max' => 100],
            [['comment'], 'string'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
			[['customer_id'], 'required'],
			[['status'], 'required', 'on' => self::SCENARIO_UPDATE],
        ];
		// Allow to edit user_id if user is admin
		if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin) {
			$rules[] = [['user_id'], 'required', 'on' => self::SCENARIO_UPDATE];
			$rules[] = [['user_id'], 'integer', 'on' => self::SCENARIO_UPDATE];
			$rules[] = [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id'], 'on' => self::SCENARIO_UPDATE];
		}
		return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'ID Usuario',
            'customer_id' => 'ID Cliente',
            'discount_percentage' => 'Porcentaje de Descuento',
            'comment' => 'Comentario',
			'status' => 'Estado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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
    public function getOrderProducts()
    {
        return $this->hasMany(OrderProduct::className(), ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['id' => 'product_id'])->viaTable('order_product', ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderStatuses()
    {
        return $this->hasMany(OrderStatus::className(), ['order_id' => 'id']);
    }
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderStatus()
    {
        return $this->hasOne(OrderStatus::className(), ['order_id' => 'id'])->orderBy(['id' => SORT_DESC]);
    }
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getEnteredOrderStatus()
    {
        return $this->hasOne(OrderStatus::className(), ['order_id' => 'id'])->andWhere(['status' => OrderStatus::STATUS_ENTERED]);
    }
	
	/**
	 * Content for the exported txt file
	 * @return string
	 */
	public function getTxt() {
		$output = '';
		$date = Yii::$app->formatter->asDatetime($this->enteredOrderStatus->create_datetime, 'ddMMyy');
		$customerGecomId = $this->customer->gecom_id;
		$userGecomId = $this->user->gecom_id;
		$orderProducts = $this->getOrderProducts()->with(['product'])->all();
		foreach ($orderProducts as $orderProduct) {
			$line = str_pad($date, 13) .
					str_pad($customerGecomId, 11) .
					str_pad($orderProduct->quantity . $orderProduct->product->gecom_code, 19) .
					str_pad(Yii::$app->formatter->asCurrency($orderProduct->price, ''), 9) .
					$userGecomId;
			$output .= $line . "\n";
		}
		return $output;
	}
	
	/**
	 * Name for the exported txt file
	 * @return string
	 */
	public function getTxtName() {
		return 'nota_pedido_' . $this->id . '.txt';
	}
}
