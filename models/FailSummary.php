<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class FailSummary extends IssueProduct {

	/**
	 * @var integer
	 */
	public $total_quantity;

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
	public function scenarios()
	{
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'total_quantity' => 'Cantidad',
			'fromDate' => 'Desde',
			'toDate' => 'Hasta',
		];
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 * @param array $groupBy
	 *
	 * @return ActiveDataProvider
	 */
	public function search($params, $groupBy = ['product_id', 'fail_id'])
	{
		$query = self::find()->select($groupBy + ['total_quantity' => 'SUM(quantity)'])
			->groupBy($groupBy)->andWhere(['and', ['not', ['fail_id' => null]], ['not', ['quantity' => null]]]);

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		// grid filtering conditions
		$query->andFilterWhere([
			'product_id' => $this->product_id,
			'fail_id' => $this->fail_id,
		]);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		$query->joinWith([
			'fail',
			'product',
			'issue.openIssueStatus' => function ($query) {
				if ($this->fromDate) {
					$query->andWhere(['>=', 'create_datetime', gmdate('Y-m-d', strtotime($this->fromDate))]);
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
