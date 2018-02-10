<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Enrolment;
use common\models\Location;
use Yii;

/**
 * EnrolmentSearch represents the model behind the search form about `common\models\Enrolment`.
 */
class EnrolmentSearch extends Enrolment
{
    public $showAllEnrolments = false;
    public $program;
    public $course;
    public $student;
    public $user_profile;
    public $teacher;
    public $startdate;
    public $startBeginDate;
    public $startEndDate;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'courseId', 'studentId', 'isDeleted'], 'integer'],
            [['showAllEnrolments','program','course','student','startdate','teacher','startBeginDate',
                            'startEndDate'], 'safe']
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
        $query = Enrolment::find()
            ->joinWith(['course' => function ($query) use ($locationId) {
                $query->location($locationId)
                        ->confirmed();
            }])
            ->notDeleted()
            ->isConfirmed()
                        ->isRegular();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->joinWith('student');
        $query->leftJoin(['program p'], 'course.programId = p.id');
        $query->leftJoin(['user_profile up'], 'course.teacherId=up.user_id');
        $dataProvider->setSort([
            'attributes' => [
                'startdate' => [
                    'asc' => ['course.startDate' => SORT_ASC],
                    'desc' => ['course.startDate' => SORT_DESC],
                ],
                'program' => [
                    'asc' => ['p.name' => SORT_ASC],
                    'desc' => ['p.name' => SORT_DESC],
                ],
                'student' => [
                    'asc' => ['student.first_name' => SORT_ASC],
                    'desc' => ['student.first_name' => SORT_DESC],
                ],
                'teacher' => [
                    'asc' => ['up.firstname' => SORT_ASC],
                    'desc' => ['up.firstname' => SORT_DESC],
                ]
            ]
        ]);
         
        $query->andFilterWhere(['p.id' => $this->program]);
        $query->andFilterWhere(['student.id' => $this->student]);
        $query->andFilterWhere(['up.user_id' => $this->teacher]);
        if ($this->startdate) {
            list($this->startBeginDate, $this->startEndDate) = explode(' - ', $this->startdate);
            $query->andWhere(['between', 'DATE(course.startDate)',
                    (new \DateTime($this->startBeginDate))->format('Y-m-d'),
                    (new \DateTime($this->startEndDate))->format('Y-m-d')]);
        }
        if (! $this->showAllEnrolments) {
            $query->andWhere(['>=', 'DATE(course.endDate)', (new \DateTime())->format('Y-m-d')])
                ->isConfirmed()
                ->isRegular()
                ->orderBy(['course.endDate' => SORT_ASC]);
        }

        return $dataProvider;
    }
}
