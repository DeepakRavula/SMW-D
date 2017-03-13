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
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application', 'category', 'event', 'created_at', 'createdUserId'], 'safe'],
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
	
}
