<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Location;
use common\models\Lesson;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class PaymentFormLessonSearch extends Lesson
{
    public $dateRange;
    public $student;
    public $toDate;
    public $fromDate;
    public $lessonId;
    public $lessonIds;
    public $showCheckBox;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['showCheckBox', 'dateRange', 'lessonId', 'fromDate', 'toDate',
                'lessonIds', 'student'], 'safe'],
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
        if ($this->dateRange) {
            list($this->fromDate, $this->toDate) = explode(' - ', $this->dateRange);
            $fromDate = new \DateTime($this->fromDate);
            $toDate = new \DateTime($this->toDate);
        }
        if ($this->lessonId) {
            $lesson = Lesson::findOne($this->lessonId);
            $userId = $lesson->customer->id;
        }
        $lessonsQuery = Lesson::find();
        if ($this->lessonIds) {
            $lessonsQuery->andWhere(['id' => $this->lessonIds]);
        } else if ($this->dateRange) {
            $query = Lesson::find()
                ->notDeleted()
                ->isConfirmed()
                ->notCanceled()
                ->between($fromDate, $toDate)
                ->privateLessons()
                ->customer($userId)
                ->unInvoiced();
            if ($this->student) {
                $query->student($this->student);
            }
            $allLessons = $query->all();
            $lessonIds = [];
            foreach ($allLessons as $lesson) {
                if ($lesson->isOwing($lesson->enrolment->id)) {
                    $lessonIds[] = $lesson->id;
                }
            }
            $lessonsQuery->andWhere(['id' => $lessonIds]);
        }
       
        return $lessonsQuery;
    }
}
