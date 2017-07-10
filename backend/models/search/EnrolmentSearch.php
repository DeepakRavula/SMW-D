<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Enrolment;
use Yii;
/**
 * EnrolmentSearch represents the model behind the search form about `common\models\Enrolment`.
 */
class EnrolmentSearch extends Enrolment
{
    public $showAllEnrolments = false;
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
		$locationId = Yii::$app->session->get('location_id');
        $query = Enrolment::find()
			->joinWith(['course' => function($query) use($locationId) {
				$query->location($locationId);
			}])
			->notDeleted()
			->isConfirmed()
                        ->isRegular();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

		 if (! $this->showAllEnrolments) {
				$query->andWhere(['>=', 'DATE(course.endDate)', (new \DateTime())->format('Y-m-d')])
				->isConfirmed()
                                ->isRegular();
        }

        return $dataProvider;
    }
}
