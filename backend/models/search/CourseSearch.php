<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Course;

/**
 * CourseSearch represents the model behind the search form about `common\models\Course`.
 */
class CourseSearch extends Course
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'programId', 'teacherId', 'locationId', 'day'], 'integer'],
            [['fromTime', 'duration', 'startDate', 'endDate'], 'safe'],
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
        $query = Course::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'programId' => $this->programId,
            'teacherId' => $this->teacherId,
            'locationId' => $this->locationId,
            'day' => $this->day,
            'fromTime' => $this->fromTime,
            'duration' => $this->duration,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);

        return $dataProvider;
    }
}
