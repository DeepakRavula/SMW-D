<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Payment;
use Yii;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class PaymentSearch extends Payment
{
    public $fromDate;
    public $toDate;
    public $status;
    public $query;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fromDate', 'toDate', 'status', 'query'], 'safe'],
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
     * @return ActiveDataProvider
     */
    public function search($params)
    {
		$locationId = Yii::$app->session->get('location_id');
        $this->fromDate = date('1-m-Y');
        $this->toDate = date('31-m-Y');
        $query = Payment::find()
			->location($locationId);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $this->fromDate = \DateTime::createFromFormat('d-m-Y', $this->fromDate);
        $this->toDate = \DateTime::createFromFormat('d-m-Y', $this->toDate);

        $query->andWhere(['between', 'date', $this->fromDate->format('Y-m-d'), $this->toDate->format('Y-m-d')]);

        //$query->andFilterWhere(['type' => $this->type]);

        return $dataProvider;
    }
}
