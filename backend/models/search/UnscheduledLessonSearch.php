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
   
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['student', 'program', 'teacher'], 'safe'],
           
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
			->notExpired()
			->joinWith(['privateLesson'])
            ->orderBy(['private_lesson.expiryDate' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if (!empty($params) && !($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        

		$query->joinWith('student');
		$query->joinWith('program');
		$query->andFilterWhere(['student.id' => $this->student]);
        $query->andFilterWhere(['program.id' => $this->program]);

        if (!empty($this->teacher)) {
            $query->joinWith(['teacherProfile' => function ($query) {
		    $query->joinWith(['user' => function($query) {
                $query->andFilterWhere(['user.id' => $this->teacher
                        ]);
		}]);
            }]);
        }
        return $dataProvider;
    }
}