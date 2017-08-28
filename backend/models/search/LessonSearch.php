<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Lesson;
use common\models\Invoice;

/**
 * LessonSearch represents the model behind the search form about `common\models\Lesson`.
 */
class LessonSearch extends Lesson
{
    const STATUS_INVOICED = 'invoiced';
	
    public $lessonStatus;
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
	/**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'courseId', 'teacherId', 'status', 'isDeleted'], 'integer'],
            [['date', 'showAllReviewLessons', 'summariseReport'], 'safe'],
            [['lessonStatus', 'fromDate', 'toDate', 'type', 'customerId', 'invoiceType','dateRange', 'student', 'program',], 'safe'],
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
        $session = Yii::$app->session;
        $locationId = $session->get('location_id');
        $query = Lesson::find()
			->isConfirmed()
			->notDeleted()
			->location($locationId)
			->activePrivateLessons()
			->andWhere(['NOT IN', 'lesson.status', [Lesson::STATUS_CANCELED, Lesson::STATUS_MISSED]])
                ->orderBy(['lesson.date' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!empty($params) && !($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
		$query->andFilterWhere(['OR', 
			['student.first_name' => $this->student],
			['student.last_name' => $this->student]
		]);
		$query->andFilterWhere(['program.name' => $this->program]);
        if (!empty($this->customerId)) {
            $query->student($this->customerId);
        }
        if (!empty($this->invoiceType)) {
            if ((int) $this->invoiceType === Invoice::TYPE_PRO_FORMA_INVOICE) {
                $query->unInvoicedProForma()
                    ->scheduled();
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
        } elseif ($this->lessonStatus === self::STATUS_INVOICED) {
            $query->invoiced();
        } elseif ((int)$this->lessonStatus === Lesson::STATUS_UNSCHEDULED) {
            $query->andFilterWhere(['lesson.status' => Lesson::STATUS_UNSCHEDULED]);
        }
        if (!empty($this->dateRange)) {
            list($this->fromDate, $this->toDate) = explode(' - ', $this->dateRange);

            $this->fromDate = \DateTime::createFromFormat('M d,Y', $this->fromDate);
            $this->toDate = \DateTime::createFromFormat('M d,Y', $this->toDate);

            if ((int) $this->invoiceType !== Invoice::TYPE_INVOICE) {
                $query->andWhere(['between', 'DATE(lesson.date)', $this->fromDate->format('Y-m-d'), $this->toDate->format('Y-m-d')]);
            }
        } else {
            $this->fromDate = \DateTime::createFromFormat('M d,Y', $this->fromDate);
            $this->toDate = \DateTime::createFromFormat('M d,Y', $this->toDate);

            if ((int) $this->invoiceType !== Invoice::TYPE_INVOICE) {
                $query->andWhere(['between', 'DATE(lesson.date)', (new \DateTime($this->fromDate))->format('Y-m-d'), (new \DateTime($this->toDate))->format('Y-m-d')]);
            }
        }

        return $dataProvider;
    }

    public static function lessonStatuses()
    {
        return [
            Lesson::STATUS_COMPLETED => 'Completed',
            Lesson::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_INVOICED => 'Invoiced',
			Lesson::STATUS_UNSCHEDULED => 'Unscheduled'
        ];
    }
}
