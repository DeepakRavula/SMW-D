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
            [['startDate', 'endDate', 'customer', 'dateRange', 'amount', 'user_id', 'paymentMethod'], 'safe'],
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
        $locationId          = Location::findOne(['slug' => \Yii::$app->location])->id;
        $query               = Payment::find()
            ->location($locationId)
            ->andWhere(['NOT', ['payment_method_id' => [PaymentMethod::TYPE_CREDIT_USED, PaymentMethod::TYPE_CREDIT_APPLIED]]])
            ->notDeleted();
           
        $query->joinWith('userProfile');
        $query->joinWith('paymentMethod');
        $dataProvider        = new ActiveDataProvider([
            'query' => $query,
        ]);
          
        if(!empty($this->dateRange)) {
            list($this->startDate, $this->endDate) = explode(' - ', $this->dateRange);
            $query->andWhere(['between', 'DATE(payment.date)',
                    (new \DateTime($this->startDate))->format('Y-m-d'),
                    (new \DateTime($this->endDate))->format('Y-m-d')]);  
        }
        $dataProvider->setSort([
            'attributes' => [
                'customer' => [
                    'asc' => ['user_profile.firstname' => SORT_ASC],
                    'desc' => ['user_profile.firstname' => SORT_DESC],
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
        $dataProvider->sort->defaultOrder = [
            'dateRange' => SORT_ASC,
        ];
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        if(!empty($this->dateRange)) {
            list($this->startDate, $this->endDate) = explode(' - ', $this->dateRange);
            $query->andWhere(['between', 'DATE(payment.date)',
                (new \DateTime($this->startDate))->format('Y-m-d'),
                (new \DateTime($this->endDate))->format('Y-m-d')]); 
        }

        $query->andFilterWhere(['like', 'payment_method.name', $this->paymentMethod]);
        $query->andFilterWhere(['like', 'amount', $this->amount]);
        $query->andFilterWhere(['user_profile.user_id' => $this->customer]);
        
        return $dataProvider;
    }
}
