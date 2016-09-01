<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\GroupLesson;

/**
 * GroupLessonSearch represents the model behind the search form about `common\models\GroupLesson`.
 */
class GroupLessonSearch extends GroupLesson
{ 
    public $fromDate;
    public $toDate;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'course_id', 'teacher_id', 'status'], 'integer'],
            [['date', 'fromDate', 'toDate'], 'safe'],
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
        $currentMonth = new \DateTime();
		$this->fromDate = $currentMonth->format('1-m-Y');
        $this->toDate = $currentMonth->format('30-m-Y');
        $location_id = Yii::$app->session->get('location_id');
		$query = GroupLesson::find()
				->joinWith('groupCourse')
				->where(['location_id' => $location_id]);	

        $groupLessonDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $groupLessonDataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'course_id' => $this->course_id,
            'teacher_id' => $this->teacher_id,
            'date' => $this->date,
            'status' => $this->status,
        ]);

        $this->fromDate =  \DateTime::createFromFormat('d-m-Y', $this->fromDate);
		$this->toDate =  \DateTime::createFromFormat('d-m-Y', $this->toDate);
        
		$query->andWhere(['between','date', $this->fromDate->format('Y-m-d'), $this->toDate->format('Y-m-d')]);
        
        return $groupLessonDataProvider;
    }
}
