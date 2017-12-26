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
    public $query;
    public $showAllCourses;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'programId', 'teacherId', 'locationId'], 'integer'],
            [['startDate', 'endDate', 'query', 'showAllCourses'], 'safe'],
            ['showAllCourses', 'boolean'],
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
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
        $query = Course::find()
                ->groupProgram($locationId)
				->confirmed();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->joinWith(['teacher' => function ($query) use ($locationId) {
            $query->joinWith('userProfile up');
        }]);
        $query->joinWith('program');

        $query->andFilterWhere([
            'id' => $this->id,
            'programId' => $this->programId,
            'teacherId' => $this->teacherId,
            'locationId' => $this->locationId,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);

        $query->andFilterWhere(['like', 'program.name', $this->query]);
        $query->orFilterWhere(['like', 'up.firstname', $this->query]);
        $query->orFilterWhere(['like', 'up.lastname', $this->query]);

        if (!$this->showAllCourses) {
            $currentDate = (new \DateTime())->format('Y-m-d H:i:s');
            $query->andWhere(['>=', 'endDate', $currentDate]);
        }

        return $dataProvider;
    }
}
