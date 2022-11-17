<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Tax;

/**
 * TaxSearch represents the model behind the search form about `common\models\Tax`.
 */
class TaxTypeSearch extends TaxType
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'province_id'], 'integer'],
            [['tax_rate'], 'number'],
            [['from_date', 'to_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied.
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Tax::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'province_id' => $this->province_id,
            'tax_rate' => $this->tax_rate,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
        ]);

        return $dataProvider;
    }
}
