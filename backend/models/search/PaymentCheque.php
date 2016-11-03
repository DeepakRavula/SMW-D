<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PaymentCheque as PaymentChequeModel;

/**
 * PaymentCheque represents the model behind the search form about `common\models\PaymentCheque`.
 */
class PaymentCheque extends PaymentChequeModel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'payment_id', 'number'], 'integer'],
            [['date', 'bank_name', 'bank_branch_name'], 'safe'],
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
        $query = PaymentChequeModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'payment_id' => $this->payment_id,
            'number' => $this->number,
            'date' => $this->date,
        ]);

        $query->andFilterWhere(['like', 'bank_name', $this->bank_name])
            ->andFilterWhere(['like', 'bank_branch_name', $this->bank_branch_name]);

        return $dataProvider;
    }
}
