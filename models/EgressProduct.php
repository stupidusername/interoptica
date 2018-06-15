<?php

namespace app\models;

use Yii;
use yii\helpers\Html;
use yii\validators\UniqueValidator;

/**
 * This is the model class for table "egress_product".
 *
 * @property int $id
 * @property int $egress_id
 * @property int $product_id
 *
 * @property Egress $egress
 * @property Product $product
 * @property EgressProductBatch[] $egressProductBatches
 */
class EgressProduct extends \yii\db\ActiveRecord
{
    public $quantity;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'egress_product';
    }

    /**
  	* @inheritdoc
  	*/
  	public static function find() {
  		$subquery = EgressProductBatch::find()->select('SUM(quantity)')->andWhere('egress_product_id='. self::tableName() . '.id')->groupBy('egress_product_id');
  		return parent::find()->addSelect([self::tableName() . '.*', 'quantity' => $subquery]);
  	}

    /**
  	 * @inheritdoc
  	 */
  	public function afterSave($insert, $changedAttributes) {
  		// Update product stock
  		if (!$insert) {
  			// Restore stock during update
  			$this->restoreStock(true);
  		}
  		$this->updateStock();
  		parent::afterSave($insert, $changedAttributes);
  	}

    /**
  	 * @inheritdoc
  	 */
  	public function beforeDelete() {
  		if (!parent::beforeDelete()) {
  				return false;
  		}
  		$this->restoreStock(true);
  		return true;
  	}

    /**
  	 * @inheritdoc
  	 */
  	public function rules()
  	{
  		return [
  			[['product_id', 'quantity'], 'required'],
  			[['product_id'], 'integer'],
  			[['quantity'], 'integer', 'min' => 1],
  			[['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
  			[['product_id'], 'uniqueEntry'],
  			[['quantity'], 'inStock'],
  		];
  	}

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'egress_id' => 'ID Egreso',
            'product_id' => 'ID Producto',
            'quantity' => 'Cantidad',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEgress()
    {
        return $this->hasOne(Egress::className(), ['id' => 'egress_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEgressProductBatches()
    {
        return $this->hasMany(EgressProductBatch::className(), ['egress_product_id' => 'id']);
    }

    /**
  	 * Validates that entered quantity it's available
  	 * @param string $attribute the attribute currently being validated
  	 * @param mixed $params the value of the "params" given in the rule
  	 * @param \yii\validators\InlineValidator related InlineValidator instance.
  	 * This parameter is available since version 2.0.11.
  	 */
  	public function inStock($attribute, $param, $validator) {
  		$stock = $this->product->stock !== null ? $this->product->stock : 0;
  		$realStock = $stock;
  		if (isset($this->oldAttributes['product_id']) && $this->product_id == $this->oldAttributes['product_id']) {
  			$realStock += $this->getEgressProductBatches()->sum('quantity');
  		}
  		if ($this->$attribute > $realStock) {
  			$this->addError($attribute, "El stock de este producto es de $realStock.");
  		}
  	}

    /**
  	 * Validates that entered entry it's unique
  	 * @param string $attribute the attribute currently being validated
  	 * @param mixed $params the value of the "params" given in the rule
  	 * @param \yii\validators\InlineValidator related InlineValidator instance.
  	 * This parameter is available since version 2.0.11.
  	 */
  	public function uniqueEntry($attribute, $param, $validator) {
  		$validator = new UniqueValidator([
  			'targetAttribute' => ['egress_id', 'product_id'],
  		]);
  		$validator->validateAttribute($this, $attribute);
  		if ($this->hasErrors($attribute)) {
  			$this->clearErrors($attribute);
  			$this->addError($attribute, 'El producto ya se encuentra en el egreso. ' .
  				Html::a('Editar', ['/egress/update-entry', 'egressId' => $this->egress_id, 'productId' => $this->product_id], ['class' => 'productUpdate']) . '.');
  		}
    }

    /**
  	 * Restores stock in case of record deletion.
  	 */
  	public function restoreStock($delete = false) {
  		$egressProductBatches = $this->getEgressProductBatches()->with(['batch'])->all();
  		foreach ($egressProductBatches as $egressProductBatch) {
  			$egressProductBatch->batch->updatestock($egressProductBatch->quantity);
  			if ($delete) {
  				$egressProductBatch->delete();
  			}
  		}
  	}

  	/**
  	* Update product stock
  	* @param integer $productId
  	* @param integer $quantity
  	*/
  	private function updateStock() {
  		$quantity = $this->quantity;
  		$batches = Batch::find()->andWhere(['product_id' => $this->product_id])->andWhere(['>', 'stock', 0])->orderBy(['id' => SORT_ASC])->all();
  		foreach ($batches as $batch) {
  			if ($quantity <= $batch->stock) {
  				$removedQuantity = $quantity;
  			} else {
  				$removedQuantity = $batch->stock;
  			}
  			$batch->updateStock(-$removedQuantity);
  			$egressProductBatch = new EgressProductBatch();
  			$egressProductBatch->egress_product_id = $this->id;
  			$egressProductBatch->batch_id = $batch->id;
  			$egressProductBatch->quantity = $removedQuantity;
  			$egressProductBatch->save(false);
  			$quantity -= $removedQuantity;
  			if ($quantity <= 0) {
  				break;
  			}
  		}
  		if ($quantity) {
  			$batch = Batch::find()->andWhere(['product_id' => $this->product_id])->orderBy(['id' => SORT_DESC])->one();
  			if ($batch) {
  				$batch->updateStock(-$quantity);
  			} else {
  				$batch = new Batch();
  				$batch->product_id = $this->product_id;
  				$batch->entered_date = gmdate('Y-m-d');
  				$batch->quantity = 0;
  				$batch->stock = -$quantity;
  				$batch->save(false);
  			}
  			$egressProductBatch = new EgressProductBatch();
  			$egressProductBatch->egress_product_id = $this->id;
  			$egressProductBatch->batch_id = $batch->id;
  			$egressProductBatch->quantity = $quantity;
  			$egressProductBatch->save(false);
  		}
  	}
}
