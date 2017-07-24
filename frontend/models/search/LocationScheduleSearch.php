<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Lesson;
use common\models\Location;
use Yii;
/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class LocationScheduleSearch extends Lesson
{

    public $locationId;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['locationId'], 'safe'],
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
        $query = Lesson::find()
			->andWhere(['lesson.status' => Lesson::STATUS_SCHEDULED,
				'DATE(date)' => (new \DateTime())->format('Y-m-d')	
			])
			->notDraft()
			->notDeleted()
			->orderBy(['TIME(date)' => SORT_ASC]);
        $dataProvider= new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
		$query->location($this->locationId);

        return $dataProvider;
    }
}
