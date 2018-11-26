<?php

namespace backend\models\search;

use Yii;
use common\models\Location;
use common\models\Enrolment;
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
        $currentdate= $currentDate = new \DateTime();
        $currentDate = $currentdate->format('Y-m-d');
        $customers =  Enrolment::find()
            ->joinWith(['course' => function ($query) use ($locationId) {
                $query->location($locationId)
                        ->confirmed()
                        ->notDeleted();
            }])
            ->notDeleted()
            ->paymentPrefered()
            ->isConfirmed()
            ->isRegular()
            ->andWhere(['>=', 'course.endDate', $currentDate]);

        $dataProvider = new ActiveDataProvider([
            'query' => $customers,
            'pagination' => false
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        return $dataProvider;
    }
}
