<?php

namespace backend\models\search;

use common\models\TimelineEvent;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TimelineEventSearch represents the model behind the search form about `common\models\TimelineEvent`.
 */
class TimelineEventSearch extends TimelineEvent
{
	const CATEGORY_LESSON = 'lesson';
	const CATEGORY_ENROLMENT = 'enrolment';
	const CATEGORY_INVOICE = 'invoice';
	const CATEGORY_PAYMENT = 'payment';
	const ALL = 'all';

	private $fromDate;
    private $toDate;
	public $category;
	public $student;

	public function init()
    {
        $fromDate = new \DateTime('today');
        $toDate   = clone $fromDate;
        $toDate->modify('tomorrow');
        $toDate->modify('1 second ago');
		$this->fromDate = $fromDate->format('d-m-Y');
		$this->toDate = $toDate->format('d-m-Y');
		
        return parent::init();
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dateRange', 'category', 'created_at', 'createdUserId', 'student'], 'safe'],
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
		$locationId = Yii::$app->session->get('location_id');
        $query = TimelineEvent::find()
			->location($locationId);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		$query->andWhere(['between', 'DATE(created_at)', (new \DateTime($this->fromDate))->format('Y-m-d'), (new \DateTime($this->toDate))->format('Y-m-d')]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
		if ($this->category === self::CATEGORY_ENROLMENT) {
			if(!empty($this->student)) {
				$query->studentEnrolment($this->student);
			} else {
	            $query->enrolment();
			}
        } elseif ($this->category === self::CATEGORY_LESSON) {
			if(!empty($this->student)) {
				$query->studentLesson($this->student);
			} else {
            	$query->lesson();
			}
        } elseif($this->category === self::CATEGORY_INVOICE) {
			if(!empty($this->student)) {
				$query->studentInvoice($this->student);
			} else {
            	$query->invoice();
			}
        } elseif($this->category === self::CATEGORY_PAYMENT) {
			if(!empty($this->student)) {
				$query->studentPayment($this->student);
			} else {
            	$query->payment();
			}
        }
		if(!empty($this->student) && $this->category === self::ALL)	{
			$query->student($this->student);
		}
		$query->where(['between', 'DATE(timeline_event.created_at)', (new \DateTime($this->fromDate))->format('Y-m-d'), (new \DateTime($this->toDate))->format('Y-m-d')]);
		
		$query->location($locationId);
		$query->andFilterWhere(['createdUserId' => $this->createdUserId]);
		
        return $dataProvider;
    }
	public static function categories()
    {
        return [
           	self::ALL  => 'All',
            self::CATEGORY_LESSON => 'Lesson',
			self::CATEGORY_ENROLMENT => 'Enrolment',
			self::CATEGORY_INVOICE => 'Invoice',
			self::CATEGORY_PAYMENT => 'Payment',
        ];
    }
	
	public function setDateRange($dateRange)
    {
        list($fromDate, $toDate) = explode(" - ", $dateRange);
        $this->fromDate = $fromDate;
        $this->toDate   = $toDate;
    }

    public function getDateRange()
    {
        return $this->fromDate . ' - ' . $this->toDate;
    }
}
