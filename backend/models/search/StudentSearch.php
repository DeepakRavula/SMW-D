<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Student;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class StudentSearch extends Student
{
    public $showAllStudents;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'customer_id', 'showAllStudents'], 'safe'],
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
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
        $query = Student::find()->notDeleted()
                ->location($locationId);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->joinWith('customerProfile cp');
        $query->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->groupBy('student.id');

        if (!$this->showAllStudents) {
            $currentDate = (new \DateTime())->format('Y-m-d H:i:s');
            $query->enrolled($currentDate)
                ->active();
        }

        return $dataProvider;
    }
}
