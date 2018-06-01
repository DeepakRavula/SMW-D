<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UnscheduledLesson;
use common\models\Invoice;
use common\models\Location;
use common\models\Lesson;

/**
 * LessonSearch represents the model behind the search form about `common\models\Lesson`.
 */
class UnscheduledLessonSearch extends Lesson
{
   
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
           
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
        $query = Lesson::find()
            ->isConfirmed()
            ->notDeleted()
            ->location($locationId)
            ->unscheduled()
	    ->notExpired();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if (!empty($params) && !($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        return $dataProvider;
    }
}
