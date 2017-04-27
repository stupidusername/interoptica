<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\IssueProduct;

/**
 * IssueProductSearch represents the model behind the search form about `app\models\IssueProduct`.
 */
class IssueProductSearch extends IssueProduct
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'issue_id', 'product_id', 'fail_id', 'quantity'], 'integer'],
            [['comment'], 'safe'],
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
        $query = IssueProduct::find();

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
            'issue_id' => $this->issue_id,
            'product_id' => $this->product_id,
            'fail_id' => $this->fail_id,
            'quantity' => $this->quantity,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment]);
		
		$query->joinWith(['fail', 'product']);

        return $dataProvider;
    }
}