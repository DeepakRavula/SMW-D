<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Payment;
use common\models\Location;
use Yii;
use common\models\PaymentMethod;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class PaymentSearch extends Payment
{
    public $number;
    public $customer;
    public $paymentMethod;
    public $startDate;
    public $endDate;
    public $dateRange;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['startDate', 'endDate', 'customer', 'dateRange', 'amount', 'user_id', 
                'paymentMethod', 'number'], 'safe'],
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
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $query = Payment::find()
            ->location($locationId)
            ->exceptAutoPayments()
            ->notDeleted();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'customer' => [
                    'asc' => ['user_profile.firstname' => SORT_ASC],
                    'desc' => ['user_profile.firstname' => SORT_DESC],
                ],
                'number' => [
                    'asc' => ['payment.id' => SORT_ASC],
                    'desc' => ['payment.id' => SORT_DESC],
                ],
                'dateRange' => [
                    'asc' => ['payment.date' => SORT_ASC],
                    'desc' => ['payment.date' => SORT_DESC],
                ],
                'paymentMethod' => [
                    'asc' => ['payment_method.name' => SORT_ASC],
                    'desc' => ['payment_method.name' => SORT_DESC],
                ],
                'amount' => [
                    'asc' => ['amount' => SORT_ASC],
                    'desc' => ['amount' => SORT_DESC],
                ],
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            $query->andWhere(['between', 'DATE(payment.date)',
                    (new \DateTime())->format('Y-m-d'),
                    (new \DateTime())->format('Y-m-d')]); 
            return $dataProvider;
        }
        if ($this->number) {
            $query->andFilterWhere(['payment.id' => $this->number]);
        } 
        if ($this->dateRange) {
            list($this->startDate, $this->endDate) = explode(' - ', $this->dateRange);
            $query->andWhere(['between', 'DATE(payment.date)',
                (new \DateTime($this->startDate))->format('Y-m-d'),
                (new \DateTime($this->endDate))->format('Y-m-d')]); 
        }

        if ($this->paymentMethod) {
            $paymentMethod = $this->paymentMethod;
            $query->joinWith(['paymentMethod' => function ($query) use ($paymentMethod) {
                $query->andFilterWhere(['like', 'payment_method.name', $paymentMethod]);
            }]);
        }

        if ($this->amount) {
            $query->andFilterWhere(['like', 'amount', $this->amount]);
        }

        if ($this->customer) {
            $customer = $this->customer;
            $query->joinWith(['userProfile' => function ($query) use ($customer) {
                $query->andFilterWhere(['user_profile.user_id' => $customer]);
            }]);
        }
        return $dataProvider;
    }
}
