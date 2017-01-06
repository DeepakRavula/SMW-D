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
    public $searchDate;
    public $query;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['searchDate', 'query'], 'safe'],
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
        $this->searchDate = new \DateTime();
        $query = Payment::find()
			->location($locationId)
            ->groupBy('payment.payment_method_id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        if (!($this->load($params) && $this->validate())) {
            $query->andWhere(['between', 'payment.date', $this->searchDate->format('Y-m-d 00:00:00'), $this->searchDate->format('Y-m-d 23:59:59')]);
            return $dataProvider;
        }

        $this->searchDate = \DateTime::createFromFormat('d-m-Y', $this->searchDate);

        $query->andWhere(['between', 'payment.date', $this->searchDate->format('Y-m-d 00:00:00'), $this->searchDate->format('Y-m-d 23:59:59')]);

        return $dataProvider;
    }
}
