<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Lesson;
use common\models\User;
use Yii;
/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class LocationScheduleSearch extends Lesson
{

	public $date;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date'], 'safe'],
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
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $locationId= Yii::$app->session->get('location_id');
		
        $query = Lesson::find()
				->andWhere(['lesson.status' => [Lesson::STATUS_SCHEDULED, Lesson::STATUS_COMPLETED]])
			->andWhere([
				'DATE(date)' => (new \DateTime($this->date))->format('Y-m-d')	
			])
			->isConfirmed()
			->notDeleted()
			->location($locationId)
			->orderBy(['TIME(date)' => SORT_ASC]);
        $dataProvider= new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        return $dataProvider;
    }
}