<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "batch".
 *
 * @property int $id
 * @property int $product_id
 * @property string $entered_date
 * @property string $dispatch_number
 * @property string $shipment_number
 * @property int $initial_stamp_numer
 * @property int $quantity
 * @property int $stock
 *
 * @property Product $product
 */
class Batch extends \yii\db\ActiveRecord
{

    // scenarios
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'batch';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'initial_stamp_numer', 'stock'], 'integer'],
            [['quantity'], 'integer', 'min' => 1],
            [['entered_date'], 'date', 'format' => 'Y-m-d'],
            [['dispatch_number', 'shipment_number'], 'string', 'max' => 255],
            [['product_id', 'entered_date', 'quantity', 'stock'], 'required'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
            [['stock'], 'compare', 'compareAttribute' => 'quantity', 'operator' => '<=', 'type' => 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
      // Get default scenario fields
      $fields = parent::scenarios()[self::SCENARIO_DEFAULT];
      // Unset fields for create scenario
      $createFields = $fields;
      unset($createFields['stock']);
      // Unset fields for update scenario
      $updateFields = $fields;
      unset($updateFields['product_id']);
      return [
          self::SCENARIO_CREATE => $createFields,
          self::SCENARIO_UPDATE => $updateFields,
      ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'ID Producto',
            'entered_date' => 'Fecha de Ingreso',
            'dispatch_number' => 'Número de Despacho',
            'shipment_number' => 'Número de Remito',
            'initial_stamp_numer' => 'Número de Estampilla Inicial',
            'quantity' => 'Cantidad',
            'stock' => 'Stock',
        ];
    }

    /**
    * @inheritdoc
    */
    public function beforeValidate() {
      if (!parent::beforeValidate()) {
        return false;
      }
      if ($this->isNewRecord) {
        $this->stock = $this->quantity;
      }
      return true;
    }

    /**
  	* @inheritdoc
  	*/
  	public function afterSave($insert, $changedAttributes) {
      // Update product stock alerts
  		if (array_key_exists('stock', $changedAttributes)) {
        $stock = $this->product->stock;
        $oldStock = $stock - $this->stock + $changedAttributes['stock'];
  			$this->product->checkStock($oldStock, $stock);
  		}
  		parent::afterSave($insert, $changedAttributes);
  	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
  	 * Updates stock
  	 */
  	public function updateStock($quantity) {
  		if (is_null($this->stock)) {
  			$this->stock = 0;
  			$this->save(false);
  		}
      $stock = $this->product->stock;
  		$this->product->checkStock($stock, $stock + $quantity);
  		$this->updateCounters(['stock' => $quantity]);
  	}
}
