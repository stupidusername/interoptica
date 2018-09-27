<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Product;

/**
 * ProductSearch represents the model behind the search form about `app\models\Product`.
 */
class ProductSearch extends Product
{
    /**
    * @var integer
    */
    public $brandId;

    /**
    * @var integer
    */
    public $modelType;

    /**
    * @var string
    */
    public $modelName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'running_low', 'brandId', 'modelType', 'available'], 'integer'],
            [['modelName', 'code'], 'safe'],
            [['price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Product::find()->active()->innerJoinWith(['model.brand']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'price' => $this->price,
            'model.type' => $this->modelType,
            'brand.id' => $this->brandId,
        ]);

        if ($this->running_low === '1') {
          $query->andWhere(['running_low' => true]);
        } elseif ($this->running_low === '0') {
          $query->andWhere(['or', ['running_low' => false], ['running_low' => null]]);
        }
        
        if ($this->available === '1') {
          $query->andWhere(['available' => true]);
        } elseif ($this->available === '0') {
          $query->andWhere(['or', ['available' => false], ['available' => null]]);
        }

        $query->andFilterWhere(['like', 'code', $this->code]);
        $query->andFilterWhere(['like', 'model.name', $this->modelName]);

        return $dataProvider;
    }
}
