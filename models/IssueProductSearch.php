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
	 * @var string
	 */
	public $fromDate;
	
	/**
	 * @var string
	 */
	public $toDate;
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'fail_id'], 'integer'],
			[['fromDate', 'toDate'], 'string'],
        ];
    }
	
	/**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fromDate' => 'Desde',
            'toDate' => 'Hasta',
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
            'product_id' => $this->product_id,
            'fail_id' => $this->fail_id,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment]);
		
		$query->joinWith([
			'fail',
			'product',
			'issue.openIssueStatus' => function ($query) {
				if ($this->fromDate) {
					$query->andWhere(['>=', 'create_datetime', $this->fromDate]);
				}
				if ($this->toDate) {
					$toDate = gmdate('Y-m-d', strtotime($this->toDate . ' +1 day'));
					$query->andWhere(['<', 'create_datetime', $toDate]);
				}
			},
		]);

        return $dataProvider;
    }
}
