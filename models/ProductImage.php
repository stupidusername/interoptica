<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_image".
 *
 * @property int $id
 * @property int $product_id
 * @property string $filename
 * @property int $rank
 *
 * @property Product $product
 */
class ProductImage extends \yii\db\ActiveRecord
{
  const LOGO_FOLDER = '/images/brand/';

  /**
   * @inheritdoc
   */
  public static function tableName()
  {
    return 'product_image';
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getProduct()
  {
    return $this->hasOne(Product::className(), ['id' => 'product_id']);
  }

  /**
  * @return string
  */
  public function getLogoUrl() {
      $url = Yii::$app->imageCache->thumbSrc(self::LOGO_FOLDER . $this->filename);
    return $url;
  }
}
