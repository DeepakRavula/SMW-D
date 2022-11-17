<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Student;
use common\models\Location;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class StudentSearch extends Student
{
    public $showAllStudents;
    public $customer;
    public $phone;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name','customer_id', 'customer','phone','showAllStudents'], 'safe'],
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
        $query = Student::find()->notDeleted()
                ->location($locationId);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
	$query->joinWith('customerProfile');
	$query->joinWith(['customer' => function ($query) {
		$query->joinWith(['userContacts'=> function ($query) {
			$query->joinWith('phone');
		}]);
        }]);
	$dataProvider->setSort([
            'attributes' => [
		'first_name' => [
                    'asc' => ['first_name' => SORT_ASC],
                    'desc' => ['first_name' => SORT_DESC],
                ],
		'last_name' => [
                    'asc' => ['last_name' => SORT_ASC],
                    'desc' => ['last_name' => SORT_DESC],
                ],
                'customer' => [
                    'asc' => ['user_profile.firstname' => SORT_ASC],
                    'desc' => ['user_profile.firstname' => SORT_DESC],
                ],
		'phone' => [
                    'asc' => ['user_phone.number' => SORT_ASC],
                    'desc' => ['user_phone.number' => SORT_DESC],
                ],
            ]
        ]);
	$dataProvider->sort->defaultOrder = [
            'first_name' => SORT_ASC,
        ];	
        $query->andFilterWhere(['like', 'student.first_name', $this->first_name])
            ->andFilterWhere(['like', 'student.last_name', $this->last_name])
	        ->andFilterWhere(['like', 'user_phone.number', trim($this->phone)])
	        ->andFilterWhere(['or', ['like', 'user_profile.firstname', trim($this->customer)], ['like', 'user_profile.lastname', trim($this->customer)]])
            ->groupBy('student.id');

        if (!$this->showAllStudents) {
            $query->statusActive();
        }
        return $dataProvider;
    }
}
