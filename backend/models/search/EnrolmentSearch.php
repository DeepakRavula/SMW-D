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
    public $enddate;
    public $endBeginDate;
    public $endEndDate;
    public $startdate;
    public $startBeginDate;
    public $startEndDate;
    public $studentView;
    public $studentId;
    public $isAutoRenewal;
    public $goToDate;
    public $weekDate;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'courseId', 'studentId', 'isDeleted'], 'integer'],
            [['showAllEnrolments', 'program', 'course', 'student', 'startdate', 'teacher', 'endEndDate',  
            'startBeginDate', 'startEndDate', 'studentView', 'studentId', 'enddate', 'lessonCount', 'endBeginDate', 'isAutoRenewal', 'goToDate', 'weekDate'], 'safe']
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
        $currentdate = $currentDate = new \DateTime();
        $currentDate = $currentdate->format('Y-m-d');
        $currentMonthLastDate = $currentdate->format('Y-m-t');
        $query = Enrolment::find()
            ->select(['enrolment.*','COUNT(*) AS lessonCount'])
            ->joinWith(['course' => function ($query) use ($locationId, $currentDate, $currentMonthLastDate) {
                $query->location($locationId)
                      ->activeEnrolments($currentDate, $currentMonthLastDate)
                        ->confirmed()
                        ->notDeleted();
            }])
            ->notDeleted()
            ->isConfirmed()
            ->isRegular();
        if ($this->studentView) {
            if (!$this->showAllEnrolments) {
                $query->activeAndfutureEnrolments();
            }
            $query->andWhere(['enrolment.studentId' => $this->studentId]);
            $query->groupBy(['enrolment.id']);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->joinWith('student');
        $query->leftJoin(['program p'], 'course.programId = p.id');
        $query->leftJoin(['user_profile up'], 'course.teacherId=up.user_id');
		$query->leftJoin(['lesson l'], 'course.id=l.courseId');
        $dataProvider->setSort([
            'attributes' => [
                'program' => [
                    'asc' => ['p.name' => SORT_ASC],
                    'desc' => ['p.name' => SORT_DESC]
                ],
                'student' => [
                    'asc' => ['student.first_name' => SORT_ASC],
                    'desc' => ['student.first_name' => SORT_DESC]
                ],
                'teacher' => [
                    'asc' => ['up.firstname' => SORT_ASC],
                    'desc' => ['up.firstname' => SORT_DESC]
                ],
		        'startdate' => [
                    'asc' => ['course.startDate' => SORT_ASC],
                    'desc' => ['course.startDate' => SORT_DESC]
                ],
                'enddate' => [
                    'asc' => ['course.endDate' => SORT_ASC],
                    'desc' => ['course.endDate' => SORT_DESC]
                ]
            ]
        ]);
        $dataProvider->sort->defaultOrder = [
            'program' => SORT_ASC
        ];
        $query->andFilterWhere(['like', 'p.name', $this->program]);
        $query->andFilterWhere(['like', "CONCAT(student.first_name, ' ', student.last_name)", $this->student]);
        $query->andFilterWhere(['like', "CONCAT(up.firstname, ' ', up.lastname)", $this->teacher]);

        if ($this->startdate) {
            list($this->startBeginDate, $this->startEndDate) = explode(' - ', $this->startdate);
            $query->andWhere(['between', 'DATE(course.startDate)',
                    (new \DateTime($this->startBeginDate))->format('Y-m-d'),
                    (new \DateTime($this->startEndDate))->format('Y-m-d')]);
        }

        if ($this->enddate) {
            list($this->endBeginDate, $this->endEndDate) = explode(' - ', $this->enddate);
            $query->andWhere(['between', 'DATE(course.endDate)',
                    (new \DateTime($this->endBeginDate))->format('Y-m-d'),
                    (new \DateTime($this->endEndDate))->format('Y-m-d')]);
        }

        if ($this->isAutoRenewal === Enrolment::AUTO_RENEWAL_STATE_ENABLED) {
            $query->andFilterWhere(['AND', ['=', 'enrolment.isAutoRenew', 1]]);
        }
        if ($this->isAutoRenewal === Enrolment::AUTO_RENEWAL_STATE_DISABLED) {
            $query->andFilterWhere(['AND', ['=', 'enrolment.isAutoRenew', 0]]);
        }

        $lessonCntPattern = "/[<>][=]?[1-9]{1,2}/";
        $lessonCntCheck = preg_match($lessonCntPattern, $this->lessonCount);
		if (!$this->showAllEnrolments || $this->lessonCount > 0 || $lessonCntCheck == 1) {
			$query->andWhere('DATE(l.date) >= CURRENT_DATE')
			      ->andWhere('DATE(l.date) <= DATE(enrolment.endDateTime)')
                ->isConfirmed()
                ->isRegular();
        }
        if ($this->lessonCount == '0' || $this->lessonCount == '<=0') {
			$query->andWhere('DATE(enrolment.endDateTime) < CURRENT_DATE');
		}

		$query->groupBy(['l.courseId']);

        if ($this->lessonCount > 0) {
			$query->having('lessonCount='.$this->lessonCount);
		}
        if ($lessonCntCheck == 1) {
			$query->having('lessonCount'.$this->lessonCount);
		} elseif ($this->lessonCount == '<0') {
			$query->having('lessonCount'.$this->lessonCount);
		}
        return $dataProvider;
    }

    public static function autoRenew()
    {
        return [
            enrolment::AUTO_RENEWAL_STATE_ENABLED => 'Enabled',
            enrolment::AUTO_RENEWAL_STATE_DISABLED => 'Disabled'
        ];
    }
    public static function statusValue()
    {
        return [
            enrolment::STATUS_ACTIVE => 'Active',
            enrolment::STATUS_INACTIVE => 'Inactive'
        ];
    }
}
