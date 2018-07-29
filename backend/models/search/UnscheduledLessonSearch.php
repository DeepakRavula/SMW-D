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
    public $showAllExpiredLesson;
    public $studentUnscheduledLesson;
   
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['student', 'program', 'teacher', 'showAll', 'showAllExpiredLesson', 'studentUnscheduledLesson'], 'safe'],
           
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
            ->notRescheduled()
            ->joinWith(['privateLesson'])
            ->andWhere(['NOT', ['private_lesson.lessonId' => null]])
            ->orderBy(['private_lesson.expiryDate' => SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if (!empty($params) && !($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        if ($this->studentUnscheduledLesson) {
            if (!$this->showAllExpiredLesson) {
           $query->notExpired();
            } 
        }
        if (!$this->showAll) {
            $query->notExpired();
        }
        if (!empty($this->student)) {
            $query->joinWith(['student' => function($query) {
                $query->andFilterWhere(['student.id' => $this->student]);
            }]);
        }
        if (!empty($this->program)) {
            $query->joinWith(['program' => function($query) {
                $query->andFilterWhere(['program.id' => $this->program]);
            }]);
        }
        if (!empty($this->teacher)) {
		    $query->joinWith(['teacher' => function($query) {
                $query->andFilterWhere(['user.id' => $this->teacher
                        ]);
		}]);
        }
        return $dataProvider;
    }
}