<?php

namespace app\models;

use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "issue".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $customer_id
 * @property integer $order_id
 * @property integer $issue_type_id
 * @property string $comment
 * @property string $contact
 * @property integer $deleted
 *
 * @property Customer $customer
 * @property Order $order
 * @property IssueType $issueType
 * @property User $user
 * @property IssueProduct[] $issueProducts
 * @property IssueStatus[] $issueStatuses
 */
class Issue extends \yii\db\ActiveRecord
{
	/**
	 * Scenarios
	 */
	const SCENARIO_VIEW = 'view';
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
        return 'issue';
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
        return parent::find()->where(['or', [self::tableName() . '.deleted' => null], [self::tableName() . '.deleted' => 0]]);
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
			$this->status = IssueStatus::STATUS_OPEN;
		}
		$oldStatus = $this->issueStatus;
		// Save issue status if there was a change
		if (!$oldStatus || $oldStatus->status != $this->status) {
			$issueStatus = new IssueStatus();
			$issueStatus->issue_id = $this->id;
			$issueStatus->user_id = Yii::$app->user->id;
			$issueStatus->status = $this->status;
			$issueStatus->create_datetime = gmdate('Y-m-d H:i:s');
			$issueStatus->save(false);
		}
		parent::afterSave($insert, $changedAttributes);
	}
	
	/**
	 * @inheritdoc
	 */
	public function afterFind() {
		$this->status = $this->issueStatus ? $this->issueStatus->status : null;
		parent::afterFind();
	}
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['issue_type_id', 'customer_id'], 'required'],
            [['user_id', 'customer_id', 'order_id', 'issue_type_id'], 'integer'],
            [['comment'], 'string'],
            [['contact'], 'string', 'max' => 255],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id', 'customer_id' => 'customer_id']],
            [['issue_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => IssueType::className(), 'targetAttribute' => ['issue_type_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
			[['status'], 'required', 'on' => self::SCENARIO_UPDATE],
        ];
    }
	
	/**
	 * @inheritdoc
	 */
	public function scenarios() {
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_VIEW] = ['status'];
		return $scenarios;
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
            'order_id' => 'ID Pedido',
            'issue_type_id' => 'ID Tipo',
            'comment' => 'Comentario',
            'contact' => 'Contacto',
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
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIssueType()
    {
        return $this->hasOne(IssueType::className(), ['id' => 'issue_type_id']);
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
    public function getIssueProducts()
    {
        return $this->hasMany(IssueProduct::className(), ['issue_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIssueStatuses()
    {
        return $this->hasMany(IssueStatus::className(), ['issue_id' => 'id']);
    }
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getIssueStatus()
    {
        return $this->hasOne(IssueStatus::className(), ['issue_id' => 'id'])->orderBy(['id' => SORT_DESC]);
    }
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getOpenIssueStatus()
    {
        return $this->hasOne(IssueStatus::className(), ['issue_id' => 'id'])->andWhere(['status' => IssueStatus::STATUS_OPEN]);
    }
}
