<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Course;

/**
 * CourseSearch represents the model behind the search form about `common\models\Course`.
 */
class CourseSearch extends Course
{
	public $query;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'programId', 'teacherId', 'locationId', 'day'], 'integer'],
            [['fromTime', 'duration', 'startDate', 'endDate', 'query'], 'safe'],
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
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
		$locationId = Yii::$app->session->get('location_id');
        $query = Course::find()
				->groupProgram($locationId);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->joinWith(['teacher' => function($query) use($locationId){                
           $query->joinWith('userProfile up');                     
        }]);
        $query->joinWith('program'); 

        $query->andFilterWhere([
            'id' => $this->id,
            'programId' => $this->programId,
            'teacherId' => $this->teacherId,
            'locationId' => $this->locationId,
            'day' => $this->day,
            'fromTime' => $this->fromTime,
            'duration' => $this->duration,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);       
        
       $query->andFilterWhere(['like', 'program.name', $this->query]);        
       $query->orFilterWhere(['like', 'up.firstname', $this->query]);
       $query->orFilterWhere(['like', 'up.lastname', $this->query]);

        return $dataProvider;
    }
}
