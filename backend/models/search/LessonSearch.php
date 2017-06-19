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
    public $type;
    public $customerId;
    public $invoiceType;
    public $showAllReviewLessons = false;
	public $summariseReport = false;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'courseId', 'teacherId', 'status', 'isDeleted'], 'integer'],
            [['date', 'showAllReviewLessons', 'summariseReport'], 'safe'],
            [['lessonStatus', 'fromDate', 'toDate', 'type', 'customerId', 'invoiceType'], 'safe'],
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
        $previousMonth = new \DateTime();
        $previousMonth->modify('first day of last month');
        $this->fromDate = $previousMonth->format('d-m-Y');
        $currentMonth = new \DateTime();
        $currentMonth->modify('last day of this month');
        $this->toDate = $currentMonth->format('d-m-Y');
        $session = Yii::$app->session;
        $locationId = $session->get('location_id');
        $query = Lesson::find()
                ->where(['not', ['lesson.status' => Lesson::STATUS_DRAFTED]])
                ->orderBy(['lesson.date' => SORT_ASC])
                ->notDeleted()
                ->location($locationId);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!empty($params) && !($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

		$query->andWhere(['NOT', ['lesson.status' => Lesson::STATUS_CANCELED]]);
        if (!empty($this->type)) {
            if ((int) $this->type === Lesson::TYPE_PRIVATE_LESSON) {
                $query->activePrivateLessons();
            } else {
                $query->groupLessons();
            }
        }

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
            $query->unInvoiced()
                ->completed();
        } elseif ($this->lessonStatus === 'scheduled') {
            $query->scheduled();
        } elseif ($this->lessonStatus === self::STATUS_INVOICED) {
            $query->invoiced();
        } elseif ((int)$this->lessonStatus === Lesson::STATUS_UNSCHEDULED) {
            $query->andFilterWhere(['lesson.status' => Lesson::STATUS_UNSCHEDULED]);
        }

        $this->fromDate = \DateTime::createFromFormat('d-m-Y', $this->fromDate);
        $this->toDate = \DateTime::createFromFormat('d-m-Y', $this->toDate);

        if ((int) $this->invoiceType !== Invoice::TYPE_INVOICE) {
            $query->andWhere(['between', 'DATE(lesson.date)', $this->fromDate->format('Y-m-d'), $this->toDate->format('Y-m-d')]);
        }

        return $dataProvider;
    }

    public static function lessonStatuses()
    {
        return [
            'all' => 'All',
            Lesson::STATUS_COMPLETED => 'Completed',
            'scheduled' => 'Scheduled',
            self::STATUS_INVOICED => 'Invoiced',
			Lesson::STATUS_UNSCHEDULED => 'Unscheduled'
        ];
    }
}
