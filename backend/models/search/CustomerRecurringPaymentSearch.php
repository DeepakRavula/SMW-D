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

    public function rules()
    {
        return [
            [['customer'], 'safe'],
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
                $query->joinWith(['customer' => function($query) {
                    $query->joinWith('userProfile');
                    }
                ]);
        $dataProvider  = new ActiveDataProvider([
            'query' =>  $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $query->joinWith('userProfile');

        $dataProvider->setSort([
            'attributes' => [
                'customer' => [
                    'asc' => ['userProfile.firstname' => SORT_ASC],
                    'desc' => ['userProfile.firstname' => SORT_DESC]
                ],
            ]
        ]);
        $dataProvider->sort->defaultOrder = [
            'customer' => SORT_ASC
        ];
        return $dataProvider;
    }
}
