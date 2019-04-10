<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Location;
use common\models\Lesson;
use common\models\GroupLesson;
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
        $userId = $this->userId;
        $student = $this->student;
        $dueDateRange = $this->dueDateRange;
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
        $lessonsQuery = GroupLesson::find();
        
        if (isset($this->lessonIds)) {
            $lessonsQuery->andWhere(['group_lesson.lessonId' => $this->lessonIds]);
        } else {
            $invoicedLessonsQuery = GroupLesson::find()
                ->joinWith(['invoiceItemLessons' => function($query) {
                    $query->andWhere(['NOT',['invoice_item_lesson.id' => null]]);
                    $query->joinWith(['invoiceLineItem ili' => function($query) {
                        $query->notDeleted()
                        ->joinWith(['invoice in' => function($query) {
                            $query->notDeleted();
                        }]);
                    }]);
                }])
                ->joinWith(['invoiceItemsEnrolment' => function($query) {
                    $query->joinWith(['lineItem' => function($query) {
                        $query->notDeleted()
                        ->joinWith(['invoice' => function($query) {
                            $query->notDeleted();
                        }]);
                    }]);
                }]);


                $lessonsQuery->joinWith(['lesson' => function($query) {
                    $query->notDeleted()
                        ->isConfirmed()
                        ->notCanceled();
                }])
                ->joinWith(['enrolment' => function($query) use ($userId, $student) {
                    $query->notDeleted()
                        ->isConfirmed()
                        ->customer($userId);
                    if ($student) {
                        $query->student($student);
                    }
                }])
                ->leftJoin(['invoiced_lesson' => $invoicedLessonsQuery], 'group_lesson.id = invoiced_lesson.id')
                ->andWhere(['invoiced_lesson.id' => null])
                ->andWhere(['>', 'group_lesson.balance', 0.09]);
            if ($this->dueDateRange) {
                $lessonsQuery->dueBetween($fromDueDate, $toDueDate);
            } else {
                $lessonsQuery->dueLessons();
            }
        }
       
        return $lessonsQuery;
    }
}
