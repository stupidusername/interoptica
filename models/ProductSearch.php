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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'variant_id', 'stock', 'running_low'], 'integer'],
            [['gecom_code', 'gecom_desc'], 'safe'],
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
        $query = Product::find();

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
            'variant_id' => $this->variant_id,
            'price' => $this->price,
            'stock' => $this->stock,
        ]);

        if ($this->running_low === '1') {
          $query->andWhere(['running_low' => true]);
        } elseif ($this->running_low === '0') {
          $query->andWhere(['or', ['running_low' => false], ['running_low' => null]]);
        }

        $query->andFilterWhere(['like', 'gecom_code', $this->gecom_code])
            ->andFilterWhere(['like', 'gecom_desc', $this->gecom_desc]);

        return $dataProvider;
    }
}
