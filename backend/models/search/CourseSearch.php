<?php

namespace backend\models\search;

use Yii;
use common\models\Location;
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
    public $program;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'programId', 'teacherId', 'locationId'], 'integer'],
            [['startDate', 'endDate', 'query','program','showAllCourses'], 'safe'],
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
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $query = Course::find()
                ->groupProgram()
                ->location($locationId)
                ->regular()
                ->notDeleted()
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
        $query->joinWith('program p');
	$dataProvider->setSort([
            'attributes' => [
                'program' => [
                    'asc' => ['p.name' => SORT_ASC],
                    'desc' => ['p.name' => SORT_DESC],
                ],
                'rate' => [
                    'asc' => ['p.rate' => SORT_ASC],
                    'desc' => ['p.rate' => SORT_DESC],
                ],
                'teacher' => [
                    'asc' => ['up.firstname' => SORT_ASC],
                    'desc' => ['up.firstname' => SORT_DESC],
                ],
		'startDate' => [
                    'asc' => ['startDate' => SORT_ASC],
                    'desc' => ['startDate' => SORT_DESC],
                ],
		'endDate' => [
                    'asc' => ['endDate' => SORT_ASC],
                    'desc' => ['endDate' => SORT_DESC],
                ]
            ]
        ]);
	$dataProvider->sort->defaultOrder = [
            'program' => SORT_ASC,
        ];
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
