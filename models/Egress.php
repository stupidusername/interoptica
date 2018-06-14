<?php

namespace app\models;

use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "egress".
 *
 * @property int $id
 * @property int $user_id
 * @property string $create_datetime
 * @property int $reason
 * @property string $comment
 * @property int $deleted
 *
 * @property User $user
 * @property EgressProduct[] $egressProducts
 */
class Egress extends \yii\db\ActiveRecord
{
    // Reasons
    const REASON_STOCK_ADJUST = 0;
    const REASON_OUT_FACTORY = 1;
    const REASON_MARKETING = 2;
    const REASON_GIFT = 3;
    const REASON_PERSONAL = 4;
    const REASON_OUT_DIRECTORS = 5;
    const REASON_CHANGE_ENVELOPE = 6;
    const REASON_CHANGE_SALESMAN = 7;
    const REASON_CONSIGNMENT = 8;
    const REASON_IN_CASE = 9;
    const REASON_OUT_CASE = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'egress';
    }

    /**
  	 * @inheritdoc
  	 */
  	public function behaviors() {
  		return [
  			'softDeleteBehavior' => [
  				'class' => SoftDeleteBehavior::className(),
  				'softDeleteAttributeValues' => [
  					'deleted' => true,
  				],
  				'replaceRegularDelete' => true
  			],
  		];
  	}

  	/**
  	 * @inheritdoc
  	 */
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
  				$this->create_date_time = gmdate('Y-m-d H:i:s');
  			}
  			return true;
  		} else {
  			return false;
  		}
  	}

    public function afterSoftDelete() {
  		$this->restoreStock();
  	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'reason'], 'integer'],
            [['reason'], 'required'],
            [['comment'], 'string'],
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
            'create_datetime' => 'Fecha',
            'reason' => 'Motivo',
            'comment' => 'Comentario',
        ];
    }

    /**
    * @return [string]
    */
    public static function reasonLabels() {
      return [
        REASON_STOCK_ADJUST => 'Ajuste stock',
        REASON_OUT_FACTORY => 'Salida fábrica',
        REASON_MARKETING => 'Marketing',
        REASON_GIFT => 'Regalos ópticas',
        REASON_PERSONAL => 'Personal',
        REASON_OUT_DIRECTORS => 'Salida directores',
        REASON_CHANGE_ENVELOPE => 'Sobres de cambio',
        REASON_CHANGE_SALESMAN => 'Piezas por cambio - Vendedor',
        REASON_CONSIGNMENT => 'Consignación',
        REASON_IN_CASE => 'Ingreso valija',
        REASON_OUT_CASE => 'Egreso valija',
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
    public function getEgressProducts()
    {
        return $this->hasMany(EgressProduct::className(), ['egress_id' => 'id']);
    }

    /**
  	 * Restores the stock in case of record deletion.
  	 */
  	private function restoreStock() {
  		foreach ($this->egressProducts as $eggressProduct) {
  			$egressProduct->restoreStock();
  		}
  	}
}
