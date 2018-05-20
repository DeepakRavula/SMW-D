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
    public $fromDate;
    public $toDate;
    public $query;
    public $dateRange;
    public $customer;
    public $paymentMethod;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fromDate', 'toDate', 'customer', 'date', 'amount', 'user_id', 'dateRange', 'paymentMethod'], 'safe'],
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
            ->notDeleted();

        $query->joinWith('userProfile');
        $query->joinWith('paymentMethod');
        $dataProvider        = new ActiveDataProvider([
            'query' => $query,
        ]);
          
        $dataProvider->setSort([
            'attributes' => [
                'customer' => [
                    'asc' => ['user_profile.firstname' => SORT_ASC],
                    'desc' => ['user_profile.firstname' => SORT_DESC],
                ],
                'date' => [
                    'asc' => ['date' => SORT_ASC],
                    'desc' => ['date' => SORT_DESC],
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
            'date' => SORT_DESC,
        ];
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'payment_method.name', $this->paymentMethod]);
        $query->andFilterWhere(['like', 'amount', $this->amount]);
       
        return $dataProvider;
    }
}
