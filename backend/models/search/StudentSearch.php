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
	public $showAllStudents;
    public $query;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'customer_id', 'showAllStudents', 'query'], 'safe'],
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
        
        $query->andFilterWhere(['like', 'first_name', $this->query])
                ->orFilterWhere(['like', 'last_name', $this->query])
                ->orFilterWhere(['like', 'cp.firstname', $this->query])
                ->orFilterWhere(['like', 'cp.lastname', $this->query]);
        
       	if(! $this->showAllStudents) { 
            $currentDate = (new \DateTime())->format('Y-m-d H:i:s');
			$query->joinWith(['groupEnrolments ge'=>function($query) {
                        $query->joinWith('groupCourse gc'); 
			       }])
                   ->joinWith('enrolment e')
                   ->andWhere(['OR',[
                       'NOT', ['e.student_id' => null]],['NOT', ['ge.student_id' => null]
                       ]]) 
                   ->andWhere(['OR',
                       ['>=','e.renewal_date', $currentDate],['>=','gc.end_date', $currentDate]
                       ]);
		} 
        
        return $dataProvider;
    }
}
