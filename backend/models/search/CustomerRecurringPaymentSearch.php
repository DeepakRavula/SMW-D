<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CustomerRecurringPayment;
use common\models\Location;

/**
 * CitySearch represents the model behind the search form about `common\models\City`.
 */
class CustomerRecurringPaymentSearch extends CustomerRecurringPayment
{
    /**
     * {@inheritdoc}
     */
    public $customer;
    public $showAll;

    public function rules()
    {
        return [
            [['customer','showAll'], 'safe'],
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
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $query = CustomerRecurringPayment::find()
                ->notDeleted()
                ->location($locationId);
        
        $query->joinWith('userProfile');      
                
        $dataProvider  = new ActiveDataProvider([
            'query' =>  $query,
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'customer' => [
                    'asc' => ['user_profile.firstname' => SORT_ASC],
                    'desc' => ['user_profile.firstname' => SORT_DESC]
                ],
                'expiryDate' => [
                    'asc' => ['customer_recurring_payment.expiryDate' => SORT_ASC],
                    'desc' => ['customer_recurring_payment.expiryDate' => SORT_DESC]
                ],
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        if (!$this->showAll) {
            $query->isRecurringPaymentEnabled();
        } 

       
        $dataProvider->sort->defaultOrder = [
            'customer' => SORT_ASC
        ];
        return $dataProvider;
    }
}
