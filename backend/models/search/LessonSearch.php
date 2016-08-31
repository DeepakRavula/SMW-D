<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;
use common\models\Lesson;
use common\models\Invoice;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class LessonSearch extends Lesson
{
	const STATUS_INVOICED = 'invoiced';

    public $lessonStatus = Lesson::STATUS_COMPLETED;
    public $fromDate = '1-1-2016';
	public $toDate = '31-12-2016';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lessonStatus', 'fromDate', 'toDate'], 'safe'],
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
     * @return ActiveDataProvider
     */
    public function search($params)
    {
		$session = Yii::$app->session;
		$locationId = $session->get('location_id');
        $query = Lesson::find()->alias('l')->location($locationId);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!empty($params) && !($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
		
        if($this->lessonStatus == Lesson::STATUS_COMPLETED) {
			$query->completed();
		} else if($this->lessonStatus === 'scheduled') {
			$query->scheduled();
		} else if($this->lessonStatus === self::STATUS_INVOICED) {
			$query->invoiced();
		} else if($this->lessonStatus === 'canceled') {
			$query->andFilterWhere(['l.status' => Lesson::STATUS_CANCELED]);
		}
        
        $this->fromDate =  \DateTime::createFromFormat('d-m-Y', $this->fromDate);
		$this->toDate =  \DateTime::createFromFormat('d-m-Y', $this->toDate);
        
		$query->andWhere(['between','l.date', $this->fromDate->format('Y-m-d'), $this->toDate->format('Y-m-d')]);

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
