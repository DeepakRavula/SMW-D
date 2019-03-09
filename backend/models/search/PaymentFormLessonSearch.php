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
    public $userId;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['showCheckBox', 'dateRange', 'lessonId', 'fromDate', 'toDate',
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
        $lessonsQuery = Lesson::find();
        if (isset($this->lessonIds)) {
            $lessonsQuery->andWhere(['id' => $this->lessonIds]);
        } else if ($this->dateRange) {
            $invoicedLessons = Lesson::find()
                ->notDeleted()
                ->isConfirmed()
                ->notCanceled()
                ->between($fromDate, $toDate)
                ->privateLessons()
                ->customer($this->userId)
                ->joinWith(['privateLesson' => function($query) {
                    $query->andWhere(['>', 'private_lesson.balance', 0.00]);
                }])
                ->invoiced();
            $lessonsQuery = Lesson::find()
                ->notDeleted()
                ->isConfirmed()
                ->notCanceled()
                ->between($fromDate, $toDate)
                ->privateLessons()
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
