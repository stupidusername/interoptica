<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Issue;

/**
 * IssueSearch represents the model behind the search form about `app\models\Issue`.
 */
class IssueSearch extends Issue
{
	/**
	 * @var integer
	 */
	public $status;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id', 'user_id', 'customer_id', 'order_id', 'issue_type_id', 'status'], 'integer'],
			[['contact'], 'safe'],
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
		$query = Issue::find()->with(['user', 'customer', 'issueType', 'openIssueStatus', 'issueStatus', 'issueStatuses']);

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
			// disambiguate if needed
			$this->status != null ? 'issue.user_id' : 'user_id' => $this->user_id,
			'customer_id' => $this->customer_id,
			'order_id' => $this->order_id,
			'issue_type_id' => $this->issue_type_id,
			'deleted' => $this->deleted,
		]);

		$status = null;
		if ($this->status != null) {
			// STATUS_OPEN should match STATUS_OPEN_URGENT too
			if ($this->status == IssueStatus::STATUS_OPEN) {
				$status = [IssueStatus::STATUS_OPEN, IssueStatus::STATUS_OPEN_URGENT];
			} else {
				$status = $this->status;
			}
		}

		if ($status) {
			$subQuery = IssueStatus::getLastStatuses();
			$subQuery2 = IssueStatus::find()->select('issue_id')->andWhere([IssueStatus::tableName() . '.id' => $subQuery, 'status' => $status]);
			$query->andWhere(['id' => $subQuery2]);
		}


		$query->andFilterWhere(['like', 'contact', $this->contact]);

		return $dataProvider;
	}
}
