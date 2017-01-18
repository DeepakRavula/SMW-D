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
    public $groupByMethod;
    public $query;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fromDate', 'toDate', 'groupByMethod', 'query'], 'safe'],
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
        $locationId          = Yii::$app->session->get('location_id');
        $query               = Payment::find()
            ->location($locationId);
        $dataProvider        = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        if (!($this->load($params) && $this->validate())) {
            $this->fromDate      = new \DateTime();
            $this->toDate        = new \DateTime();
            $query->andWhere(['between', 'payment.date', $this->fromDate->format('Y-m-d 00:00:00'), $this->toDate->format('Y-m-d 23:59:59')]);
            return $dataProvider;
        }
        if ($this->groupByMethod) {
            $query->groupBy('payment.payment_method_id');
        }
        $this->fromDate = \DateTime::createFromFormat('d-m-Y', $this->fromDate);
        $this->toDate   = \DateTime::createFromFormat('d-m-Y', $this->toDate);

        $query->andWhere(['between', 'payment.date', $this->fromDate->format('Y-m-d 00:00:00'), $this->toDate->format('Y-m-d 23:59:59')]);

        return $dataProvider;
    }
}
