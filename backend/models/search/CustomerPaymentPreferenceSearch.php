<?php

namespace backend\models\search;

use Yii;
use common\models\Location;
use common\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CustomerPaymentPreference;

/**
 * CourseSearch represents the model behind the search form about `common\models\Course`.
 */
class CustomerPaymentPreferenceSearch extends CustomerPaymentPreference
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'locationId'], 'integer'],
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
        $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
        $customers = User::find()
            ->joinWith(['customerPaymentPreference' => function ($query) {
                $query ->andWhere(['NOT', ['customer_payment_preference.id' => null]]);
            }])
        ->location($locationId)
        ->notDeleted();

        $dataProvider = new ActiveDataProvider([
            'query' => $customers,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

    

        return $dataProvider;
    }
}
