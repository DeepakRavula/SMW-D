<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ArrayDataProvider;
use common\models\Enrolment;
use Yii;
use common\models\PaymentMethod;
/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class TeacherScheduleSearch extends Enrolment
{
    public $fromDate;
    public $toDate;
    public $groupByMethod = false;
    public $query;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fromDate', 'toDate', 'groupByMethod', 'query'], 'safe'],
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
        $course_data = Enrolment::find()->all();
        $dataProvider= new ArrayDataProvider([
            'allModels' => $course_data,
            'pagination' => false,
        ]);
        return $dataProvider;
    }
}
