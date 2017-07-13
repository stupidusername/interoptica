<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\MonthlySummary;

/**
 * MonthlySummarySearch represents the model behind the search form about `app\models\MonthlySummary`.
 */
class MonthlySummarySearch extends MonthlySummary
{

	/**
	 * @var integer
	 */
	public $month;

	/**
	 * @var integer
	 */
	public $year;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['user_id', 'month', 'year'], 'integer'],
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
	public function attributeLabels() {
		$labels = [
			'month' => 'Mes',
			'year' => 'Año',
		];
		return parent::attributeLabels() + $labels;
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
		$query = self::find();

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => false,
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			'user_id' => $this->user_id,
		]);

		if ($this->year) {
			$query->andWhere(['>=', 'begin_date', $this->year . '-01-01']);
			$query->andWhere(['<=', 'begin_date', $this->year . '-12-31']);
		}

		if ($this->month) {
			$query->andWhere(['like', 'begin_date', '%-%' . $this->month . '-%', false]);
		}

		$query->with(['user']);

		$query->orderBy(['begin_date' => SORT_DESC, 'user_id' => SORT_ASC]);

		return $dataProvider;
	}
}
