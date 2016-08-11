<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Student;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class StudentSearch extends Student
{
	public $showAllStudent;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name','customer_id','showAllStudent'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $locationId = Yii::$app->session->get('location_id');
		$query = Student::find()
			->joinWith(['customer' => function($query) use($locationId){
				$query->joinWith('userLocation ul')
					->where(['ul.location_id' => $locationId]);
			}]);
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
        
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
       	if(! $this->showAllStudent) {
			$query->joinWith('enrolment e')
				->andWhere(['not', ['e.student_id' => null]]);
		} 
        $query->andFilterWhere(['like', 'first_name', $this->first_name])
              ->andFilterWhere(['like', 'last_name', $this->last_name])
              ->andFilterWhere(['like', 'customer_id', $this->customer_id]);

        return $dataProvider;
    }
}
