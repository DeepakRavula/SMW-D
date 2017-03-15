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
	const CATEGORY_USER = 'user';
	const CATEGORY_LESSON = 'lesson';
	const CATEGORY_PAYMENT = 'payment';
	const CATEGORY_ENROLMENT = 'enrolment';

	private $fromDate;
    private $toDate;


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
            [['dateRange','application', 'category', 'event', 'created_at', 'createdUserId'], 'safe'],
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
					->andWhere(['locationId' => $locationId]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		$query->andWhere(['between', 'DATE(created_at)', (new \DateTime($this->fromDate))->format('Y-m-d'), (new \DateTime($this->toDate))->format('Y-m-d')]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

		if ($this->category === self::CATEGORY_USER) {
            $query->user();
        } elseif ($this->category === self::CATEGORY_ENROLMENT) {
            $query->enrolment();
        } elseif ($this->category === self::CATEGORY_LESSON) {
            $query->lesson();
        } elseif ($this->category === self::CATEGORY_PAYMENT) {
            $query->payment();
        }
		$query->andFilterWhere(['createdUserId' => $this->createdUserId]);
		
		$query->where(['between', 'DATE(created_at)', (new \DateTime($this->fromDate))->format('Y-m-d'), (new \DateTime($this->toDate))->format('Y-m-d')]);
		
        return $dataProvider;
    }
	public static function categories()
    {
        return [
           	'all' => 'All',
            self::CATEGORY_LESSON => 'Lesson',
			self::CATEGORY_PAYMENT => 'Payment',
			self::CATEGORY_ENROLMENT => 'Enrolment',
			self::CATEGORY_USER => 'User'
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
