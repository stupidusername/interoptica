<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "issue_product".
 *
 * @property integer $id
 * @property integer $issue_id
 * @property integer $product_id
 * @property integer $fail_id
 * @property integer $quantity
 * @property string $comment
 *
 * @property Fail $fail
 * @property Issue $issue
 * @property Product $product
 */
class IssueProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'issue_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['product_id'], 'required'],
			[['fail_id'], 'required', 'when' => function ($model) { return $model->issue->issueType->required_issue_product_fail_id; }],
			[['quantity'], 'required', 'when' => function ($model) { return $model->issue->issueType->required_issue_product_quantity; }],
            [['issue_id', 'product_id', 'fail_id', 'quantity'], 'integer'],
            [['comment'], 'string'],
            [['fail_id'], 'exist', 'skipOnError' => true, 'targetClass' => Fail::className(), 'targetAttribute' => ['fail_id' => 'id']],
            [['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::className(), 'targetAttribute' => ['issue_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'issue_id' => 'ID Reclamo',
            'product_id' => 'ID Producto',
            'fail_id' => 'ID Falla',
            'quantity' => 'Cantidad',
            'comment' => 'Comentario',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFail()
    {
        return $this->hasOne(Fail::className(), ['id' => 'fail_id']);
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
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }
}
