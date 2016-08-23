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
	public $showAllStudents = false;
	public $query;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name','customer_id','showAllStudents', 'query'], 'safe'],
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
				$query->joinWith(['userLocation ul'=> function($query) use($locationId){
					$query->where(['ul.location_id' => $locationId]);
			}]);
			}]);
		$query->joinWith('customerProfile cp');
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
        
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
       	if(! $this->showAllStudents) {
			$query->joinWith('enrolment e')
				->andWhere(['not', ['e.student_id' => null]]);
		} 
        $query->andFilterWhere(['like', 'first_name', $this->query])
              ->orFilterWhere(['like', 'last_name', $this->query])
              ->orFilterWhere(['like', 'cp.firstname', $this->query])
              ->orFilterWhere(['like', 'cp.lastname', $this->query]);
        return $dataProvider;
    }
}
