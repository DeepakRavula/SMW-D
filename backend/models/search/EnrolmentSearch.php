<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Enrolment;

/**
 * EnrolmentSearch represents the model behind the search form about `common\models\Enrolment`.
 */
class EnrolmentSearch extends Enrolment
{
    public $showAllEnrolments;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'courseId', 'studentId', 'isDeleted'], 'integer'],
			[['showAllEnrolments'], 'safe']
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
        $query = Enrolment::find()
			->notDeleted()
			->isConfirmed();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

		 if ($this->showAllEnrolments) {
			 $query->joinWith(['course' => function($query) {
				$query->andWhere(['>=', 'DATE(course.endDate)', (new \DateTime())->format('Y-m-d')])
					->isConfirmed();
			 }]);
        }

        return $dataProvider;
    }
}
