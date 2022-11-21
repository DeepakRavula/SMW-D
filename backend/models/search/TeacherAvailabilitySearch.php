<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TeacherAvailability;

/**
 * TeacherAvailabilitySearch represents the model behind the search form about `common\models\TeacherAvailability`.
 */
class TeacherAvailabilitySearch extends TeacherAvailability
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'teacher_id', 'location_id', 'day'], 'integer'],
            [['from_time', 'to_time'], 'safe'],
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
        $query = TeacherAvailability::find()
                ->notDeleted();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'teacher_id' => $this->teacher_id,
            'location_id' => $this->location_id,
            'day' => $this->day,
            'from_time' => $this->from_time,
            'to_time' => $this->to_time,
        ]);

        return $dataProvider;
    }
}
