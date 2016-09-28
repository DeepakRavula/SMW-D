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
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'courseId', 'teacherId', 'status', 'isDeleted'], 'integer'],
            [['date'], 'safe'],
            [['lessonStatus', 'fromDate', 'toDate', 'type', 'customerId', 'invoiceType'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
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
				->notDeleted()
				->location($locationId);
	
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!empty($params) && !($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
		
		if(! empty($this->type)){
			if((int) $this->type === Lesson::TYPE_PRIVATE_LESSON){
				$query->activePrivateLessons()
					;
			} else {
				$query->groupLessons();
			}
		}
		
		if( ! empty($this->customerId)){
			$query->student($this->customerId);
		}
		if( ! empty($this->invoiceType)){
			if((int) $this->invoiceType === Invoice::TYPE_PRO_FORMA_INVOICE){
				$query->unInvoicedProForma()
					->scheduled();
			}else{
				$query->unInvoiced()
					->completed()
					->orderBy('lesson.id ASC');
			}
		}
        if($this->lessonStatus == Lesson::STATUS_COMPLETED) {
			$query->completed();
		} else if($this->lessonStatus === 'scheduled') {
			$query->scheduled();
		} else if($this->lessonStatus === self::STATUS_INVOICED) {
			$query->invoiced();
		} else if($this->lessonStatus === 'canceled') {
			$query->andFilterWhere(['lesson.status' => Lesson::STATUS_CANCELED]);
		}
        
        $this->fromDate =  \DateTime::createFromFormat('d-m-Y', $this->fromDate);
		$this->toDate =  \DateTime::createFromFormat('d-m-Y', $this->toDate);
       	
		if((int) $this->invoiceType !== Invoice::TYPE_INVOICE){
			$query->andWhere(['between','lesson.date', $this->fromDate->format('Y-m-d'), $this->toDate->format('Y-m-d')]);
		}

        return $dataProvider;
    }
	
	public static function lessonStatuses() {
		return [
			'all' => 'All',
			Lesson::STATUS_COMPLETED => 'Completed',
			'scheduled' => 'Scheduled',
            self::STATUS_INVOICED => 'Invoiced',
            'canceled' => 'Canceled',
		];
	}
}
