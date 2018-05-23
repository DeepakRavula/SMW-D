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
    public $createdUser;
    public $created_at;
    public $message;
    public $date;

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
            [['dateRange', 'category', 'created_at', 'createdUserId','createdUser', 'student','message' ], 'safe'],
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
        $query = LogHistory::find()->orderBy(['log.createdOn' => SORT_DESC]);
	$query->joinWith(['log' => function ($query) {
                    $query->joinWith(['logObject']);
		}]);
	$query->location($locationId);
	$dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        if (!($this->load($params) && $this->validate())) {
            $query->today();
            return $dataProvider;
        }
	
   
	$dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->location($locationId);
	if(!empty($this->createdUser))
	{
	$query->andFilterWhere(['log.createdUserId' => $this->createdUser]);
	
	}
	if(!empty($this->created_at))
	{
        list($this->fromDate, $this->toDate) = explode(' - ', $this->created_at);
		$query->andWhere(['between', 'DATE(log.createdOn)', (new \DateTime($this->fromDate))->format('Y-m-d'), (new \DateTime($this->toDate))->format('Y-m-d')]);
    }
    if(!empty($this->message))
	{
		$query->andWhere(['like', 'log.message',$this->message]);
    }
        return $dataProvider;
    } 
}
