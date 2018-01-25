<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\models\Model;

/**
 * ModelSearch represents the model behind the search form of `app\models\Model`.
 */
class ModelSearch extends Model
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'brand_id', 'front_size', 'lens_width', 'bridge_size', 'temple_length', 'base', 'flex', 'polarized', 'mirrored', 'deleted'], 'integer'],
            [['name', 'description'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return yii\base\Model::scenarios();
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
        $query = Model::find()->active();

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
            'type' => $this->type,
            'brand_id' => $this->brand_id,
            'front_size' => $this->front_size,
            'lens_width' => $this->lens_width,
            'bridge_size' => $this->bridge_size,
            'temple_length' => $this->temple_length,
            'base' => $this->base,
            'flex' => $this->flex,
            'polarized' => $this->polarized,
            'mirrored' => $this->mirrored,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description]);

        $query->with(['brand']);

        return $dataProvider;
    }
}
