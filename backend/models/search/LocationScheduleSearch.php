<?php

namespace backend\models\search;

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
    public $date;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date'], 'safe'],
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
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
	$rescheduledLessons = Lesson::find()
            ->canceled()
            ->roots()
            ->andWhere(['DATE(date)' => (new \DateTime($this->date))->format('Y-m-d')])
            ->isConfirmed()
            ->notDeleted()
            ->location($locationId)
            ->present()
	        ->all();
	$rootLessonIds = [];
	foreach ($rescheduledLessons as $rescheduledLesson) {	
		if(!empty($rescheduledLesson->leaf ) && $rescheduledLesson->leaf->isRescheduled()) {
		$rootLessonIds[] = $rescheduledLesson->id;
	    }
	}
	$query = Lesson::find()
	    ->notCanceled()
            ->andWhere(['DATE(date)' => (new \DateTime($this->date))->format('Y-m-d')])
            ->isConfirmed()
            ->notDeleted()
            ->present()
            ->location($locationId)
	    ->orWhere(['IN', 'lesson.id',$rootLessonIds])
            ->orderBy(['TIME(date)' => SORT_ASC]);
        $dataProvider= new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        return $dataProvider;
    }
}
