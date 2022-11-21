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
    public $isDefault;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['startDate', 'endDate', 'customer', 'dateRange', 'amount', 'user_id', 
                'paymentMethod', 'number', 'isShowMore', 'isDefault'], 'safe'],
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
        $query->joinWith(['userProfile']);
        $query->joinWith(['paymentMethod']);    
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
        
        if ($this->isDefault) {
            $this->startDate = (new \DateTime())->format('M d, Y');
            $this->endDate = (new \DateTime())->format('M d, Y');
            $this->dateRange = $this->startDate.' - '.$this->endDate;
        }

        if ($this->dateRange) {
            list($this->startDate, $this->endDate) = explode(' - ', $this->dateRange);
            $query->andWhere(['between', 'DATE(payment.date)',
                (new \DateTime($this->startDate))->format('Y-m-d'),
                (new \DateTime($this->endDate))->format('Y-m-d')]); 
        }

        if ($this->paymentMethod) {
            $paymentMethod = $this->paymentMethod;
                $query->andFilterWhere(['payment_method.name' => $paymentMethod]);
        }

        if ($this->amount) {
            $query->andFilterWhere(['like', 'amount', $this->amount]);
        }

        if ($this->customer) {
            $customer = $this->customer;
            $query->andFilterWhere(['like', "CONCAT(user_profile.firstname, ' ', user_profile.lastname)", $this->customer]);
        }
        return $dataProvider;
    }
}
