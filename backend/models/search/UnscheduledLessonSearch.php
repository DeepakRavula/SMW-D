<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UnscheduledLesson;
use common\models\Invoice;
use common\models\Location;
use common\models\Lesson;

/**
 * LessonSearch represents the model behind the search form about `common\models\Lesson`.
 */
class UnscheduledLessonSearch extends Lesson
{
	public $program;
    public $teacher;
    public $student;
    public $showAll;
   
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['student', 'program', 'teacher', 'showAll'], 'safe'],
           
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
        $query = Lesson::find()
            ->isConfirmed()
            ->notDeleted()
            ->location($locationId)
            ->unscheduled()
            ->joinWith(['privateLesson'])
            ->andWhere(['NOT', ['private_lesson.lessonId' => null]])
            ->orderBy(['private_lesson.expiryDate' => SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if (!empty($params) && !($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        if (!$this->showAll) {
           $query->notExpired();
       }
        if ($this->student) {
            $query->joinWith(['student' => function($query) {
                $query->andFilterWhere(['or', ['like', 'student.first_name', trim($this->student)], ['like', 'student.last_name', trim($this->student)]]);
            }]);
        }
        if ($this->program) {
            $query->joinWith(['program' => function($query) {
                $query->andFilterWhere(['like', 'program.name', $this->program]);
            }]);
        }
        if ($this->teacher) {
		    $query->joinWith(['teacherProfile' => function($query) {
                $query->andFilterWhere(['or', ['like', 'user_profile.firstname', $this->teacher], ['like','user_profile.lastname', $this->teacher]]);
		    }]);
        }
        return $dataProvider;
    }
}