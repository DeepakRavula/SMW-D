<?php

namespace backend\models\search;

use common\models\log\Log;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Location;
use common\models\log\LogHistory;
use common\models\User;
/**
 * TimelineEventSearch represents the model behind the search form about `common\models\TimelineEvent`.
 */
class TimelineEventSearch extends Log
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
	    
	$locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
	$loggedUser = User::findOne(['id' => Yii::$app->user->id]);
        $query = LogHistory::find()->today();
	$query->joinWith(['log' => function ($query) {
                    $query->joinWith(['logObject']);
		}]);
	$query->location($locationId);
	$dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
	
	if(empty($this->createdUserId))
	{
	$query->andFilterWhere(['log.createdUserId' => $loggedUser->id]);
	
	}
	if(empty($this->dateRange))
	{
	$query->andWhere(['between', 'DATE(log.createdOn)', (new \DateTime())->format('Y-m-d'), (new \DateTime())->format('Y-m-d')]);
	}
        
	if(!empty($this->createdUserId))
	{
	$query->andFilterWhere(['log.createdUserId' => $this->createdUserId])
		->orFilterWhere(['log.createdUserId' => $loggedUser->id]);
	}
	if(!empty($this->dateRange))
	{
		$query->andWhere(['between', 'DATE(log.createdOn)', (new \DateTime($this->fromDate))->format('Y-m-d'), (new \DateTime($this->toDate))->format('Y-m-d')]);
		
	}
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
