<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TaxCode;

/**
 * TaxCodeSearch represents the model behind the search form about `common\models\TaxCode`.
 */
class TaxCodeSearch extends TaxCode
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'tax_type_id', 'province_id'], 'integer'],
            [['rate'], 'number'],
            [['start_date'], 'safe'],
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
        $query = TaxCode::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'tax_type_id' => $this->tax_id,
            'province_id' => $this->province_id,
            'rate' => $this->rate,
            'start_date' => $this->start_date,
        ]);

        return $dataProvider;
    }
}
