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
            [['showCheckBox', 'dateRange', 'dueDateRange',  'lessonId', 'fromDate', 'toDate', 'fromDueDate', 'toDueDate',
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
            $invoicedLessons = Lesson::find()
                ->notDeleted()
                ->isConfirmed()
                ->notCanceled();
                if ($this->dueDateRange) {
                    $invoicedLessons->dueBetween($fromDueDate, $toDueDate);
                } else {
                    $invoicedLessons->dueLessons();
                }
                $invoicedLessons->privateLessons()
                ->customer($this->userId)
                ->invoiced();
            $lessonsQuery = Lesson::find()
                ->notDeleted()
                ->isConfirmed()
                ->notCanceled();
                if ($this->dueDateRange) {
                $query->dueBetween($fromDueDate, $toDueDate);
                } else {
                   $query->dueLessons(); 
                }
                $query->privateLessons()
                ->customer($this->userId)
                ->joinWith(['privateLesson' => function($query) {
                    $query->andWhere(['>', 'private_lesson.balance', 0.00]);
                }])
                ->leftJoin(['invoiced_lesson' => $invoicedLessons], 'lesson.id = invoiced_lesson.id')
                ->andWhere(['invoiced_lesson.id' => null]);
            if ($this->student) {
                $lessonsQuery->student($this->student);
            }
        }
       
        return $lessonsQuery;
    }
}
