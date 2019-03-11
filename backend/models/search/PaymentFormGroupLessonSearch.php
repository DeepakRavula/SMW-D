<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Location;
use common\models\Lesson;
use common\models\Enrolment;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class PaymentFormGroupLessonSearch extends Lesson
{
    public $dateRange;
    public $dueDateRange;
    public $student;
    public $toDate;
    public $fromDate;
    public $toDueDate;
    public $fromDueDate;
    public $lessonId;
    public $lessonIds;
    public $showCheckBox;
    public $userId;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['showCheckBox', 'dateRange', 'lessonId', 'fromDate', 'toDate', 'fromDueDate', 'toDueDate', 'dueDateRange',
                'lessonIds', 'student','userId'], 'safe'],
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
        if ($this->dateRange) {
            list($this->fromDate, $this->toDate) = explode(' - ', $this->dateRange);
            $fromDate = new \DateTime($this->fromDate);
            $toDate = new \DateTime($this->toDate);
        }
        if ($this->dueDateRange) {
            list($this->fromDueDate, $this->toDueDate) = explode(' - ', $this->dueDateRange);
            $fromDueDate = new \DateTime($this->fromDueDate);
            $toDueDate = new \DateTime($this->toDueDate);
        }
        $lessonsQuery = Lesson::find();
        if (isset($this->lessonIds)) {
            $lessonsQuery->andWhere(['id' => $this->lessonIds]);
        } else if ($this->dateRange) {
            $query = Lesson::find()
                ->notDeleted()
                ->isConfirmed()
                ->notCanceled();
                if ($this->dueDateRange) {
                    $query->dueBetween($fromDueDate, $toDueDate);
                } else {
                    $query->dueLessons();
                }
                $query->groupLessons()
                ->customer($this->userId);
            if ($this->student) {
                $query->student($this->student);
            }
            $allLessons = $query->all();
            $lessonIds = [];
            foreach ($allLessons as $lesson) {
                if ($this->student) {
                    $enrolment = Enrolment::find()
                        ->notDeleted()
                        ->isConfirmed()
                        ->andWhere(['courseId' => $lesson->courseId])
                        ->andWhere(['studentId' => $this->student])
                        ->one();
                } else {
                    $enrolment = Enrolment::find()
                        ->notDeleted()
                        ->isConfirmed()
                        ->andWhere(['courseId' => $lesson->courseId])
                        ->customer($this->userId)
                        ->one();
                }
                if (!$enrolment->hasinvoice($lesson->id)) {
                    if ($lesson->isOwing($enrolment->id)) {
                        $lessonIds[] = $lesson->id;
                    }
                }
            }
            $lessonsQuery->andWhere(['id' => $lessonIds]);
        }
       
        return $lessonsQuery;
    }
}
