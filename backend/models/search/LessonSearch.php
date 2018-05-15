<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Lesson;
use common\models\Invoice;
use common\models\Location;

/**
 * LessonSearch represents the model behind the search form about `common\models\Lesson`.
 */
class LessonSearch extends Lesson
{
    const STATUS_INVOICED = 'Yes';
    const STATUS_UNINVOICED='No';
    
    public $lessonStatus;
    public $invoiceStatus;
    public $fromDate;
    public $toDate;
    public $dateRange;
    public $type;
    public $customerId;
    public $invoiceType;
    public $showAllReviewLessons = false;
    public $summariseReport = false;
    public $student;
    public $program;
    public $teacher;
    public $ids;
    public $attendanceStatus;
    public $rate;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'courseId', 'teacherId', 'status', 'isDeleted'], 'integer'],
            [['date', 'showAllReviewLessons', 'summariseReport', 'ids'], 'safe'],
            [['lessonStatus', 'fromDate','invoiceStatus', 'attendanceStatus','toDate', 'type', 'customerId',
                'invoiceType','dateRange', 'rate','student', 'program', 'teacher'], 'safe'],
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
        $this->fromDate = (new \DateTime())->format('M d,Y');
        $this->toDate = (new \DateTime())->format('M d,Y');
        $this->dateRange = $this->fromDate.' - '.$this->toDate;
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $query = Lesson::find()
            ->isConfirmed()
            ->notDeleted()
            ->location($locationId)
            ->activePrivateLessons()
            ->andWhere(['NOT IN', 'lesson.status', [Lesson::STATUS_CANCELED]]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if (!empty($params) && !($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        if (!empty($this->ids)) {
            $lessonQuery = Lesson::find()
                    ->andWhere(['id' => $this->ids]);
            $dataProvider = new ActiveDataProvider([
                'query' => $lessonQuery,
            ]);
            return $dataProvider;
        }
      
        $query->andFilterWhere(['student.id' => $this->student]);
        $query->andFilterWhere(['program.id' => $this->program]);
	
        if (!empty($this->teacher)) {
            $query->joinWith(['teacherProfile' => function ($query) {
                $query->andFilterWhere([
                            'LIKE', "CONCAT(user_profile.firstname, ' ', user_profile.lastname)", $this->teacher
                        ]);
            }]);
        }
        if (!empty($this->customerId)) {
            $query->student($this->customerId);
        }
        if (!empty($this->invoiceType)) {
            if ((int) $this->invoiceType === Invoice::TYPE_PRO_FORMA_INVOICE) {
                $query->unInvoicedProForma()
                    ->scheduledOrRescheduled();
            } else {
                $query->unInvoiced()
                    ->completed()
                    ->orderBy('lesson.id ASC');
            }
        }
        if (!empty($this->courseId)) {
            $query->andFilterWhere(['lesson.courseId' => $this->courseId]);
        }
        if ($this->lessonStatus == Lesson::STATUS_COMPLETED) {
            $query->completed();
        } elseif ((int)$this->lessonStatus === Lesson::STATUS_SCHEDULED) {
            $query->scheduled();
        } elseif ((int)$this->lessonStatus === Lesson::STATUS_RESCHEDULED) {
            $query->rescheduled()
		  ->andWhere(['>=', 'lesson.date', (new \DateTime())->format('Y-m-d H:i:s')]);
;
        } elseif ((int)$this->lessonStatus === Lesson::STATUS_UNSCHEDULED) {
            $query->unscheduled();
        }
        if ($this->attendanceStatus === Lesson::STATUS_PRESENT) {
            $query->andFilterWhere(['lesson.isPresent' => true]);
        } elseif ($this->attendanceStatus === Lesson::STATUS_ABSENT) {
            $query->andFilterWhere(['lesson.isPresent' => false]);
        }
        if ($this->invoiceStatus === self::STATUS_INVOICED) {
            $query->invoiced();
        } elseif ($this->invoiceStatus === self::STATUS_UNINVOICED) {
            $query->unInvoiced();
        }
        if (!empty($this->dateRange)) {
            list($this->fromDate, $this->toDate) = explode(' - ', $this->dateRange);

            $this->fromDate = \DateTime::createFromFormat('M d,Y', $this->fromDate);
            $this->toDate = \DateTime::createFromFormat('M d,Y', $this->toDate);

            if ((int) $this->invoiceType !== Invoice::TYPE_INVOICE) {
                $query->andWhere(['between', 'DATE(lesson.date)', $this->fromDate->format('Y-m-d'), $this->toDate->format('Y-m-d')]);
            }
        }
 
        $query->joinWith('teacherProfile');
	$dataProvider->setSort([
            'attributes' => [
                'program' => [
                    'asc' => ['program.name' => SORT_ASC],
                    'desc' => ['program.name' => SORT_DESC],
                ],
		 'teacher' => [
                    'asc' => ['user_profile.firstname' => SORT_ASC],
                    'desc' => ['user_profile.firstname' => SORT_DESC],
                ],
                'student' => [
                    'asc' => ['student.first_name' => SORT_ASC],
                    'desc' => ['student.first_name' => SORT_DESC],
                ],
		'dateRange' => [
                    'asc' => ['date' => SORT_ASC],
                    'desc' => ['date' => SORT_DESC],
                ],
            ]
        ]);
	$dataProvider->sort->defaultOrder = [
            'program' => SORT_ASC,
	    ];
        return $dataProvider;
    }

    public static function lessonStatuses()
    {
        return [
            Lesson::STATUS_COMPLETED => 'Completed',
            Lesson::STATUS_SCHEDULED => 'Scheduled',
            Lesson::STATUS_RESCHEDULED => 'Rescheduled',
            Lesson::STATUS_UNSCHEDULED => 'Unscheduled'
        ];
    }
    public static function invoiceStatuses()
    {
        return [
            self::STATUS_INVOICED => 'Yes',
            self::STATUS_UNINVOICED => 'No'
        ];
    }
    public static function attendanceStatuses()
    {
        return [
            Lesson::STATUS_PRESENT => 'Yes',
            Lesson::STATUS_ABSENT=> 'No',
        ];
    }
}
