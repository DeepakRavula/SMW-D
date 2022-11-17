<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Invoice;
use Yii;
use common\models\Location;
use common\models\GroupLesson;
use common\models\Student;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class ReportGroupLessonSearch extends Invoice
{
    public $goToDate;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goToDate'], 'safe'],
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

    public function search($params)
    {
        $customerIds = [];
        $futureGroupLessonTotal = [];
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $currentDate = (new \DateTime())->format('Y-m-d');
        $students = Student::find()
            ->notDeleted()
            ->location($locationId)
            ->all();

        foreach ($students as $student) {
            $customerIds[] = $student->customer_id;
        }

        $futureGroupLessons = GroupLesson::find()
            ->joinWith(['lesson' => function ($query) use ($locationId,$currentDate) {
            $query->location($locationId)
                ->andWhere(['>=', 'DATE(lesson.date)', $currentDate])
                ->notDeleted();
        }])
            ->notDeleted()
            ->all();

        foreach ($futureGroupLessons as $groupLesson) {
            $futureGroupLessonTotal[] = $groupLesson->total;
        }

        $lessonQuery = GroupLesson::find()
            ->andWhere(['OR',
            ['group_lesson.paidStatus' => GroupLesson::STATUS_PAID],
            [
                'AND',

                ['group_lesson.paidStatus' => GroupLesson::STATUS_OWING],

                ['NOT', ['group_lesson.balance' => $futureGroupLessonTotal]]
            ]
            ])
            ->andWhere(['!=', 'group_lesson.total', 0.0000])
            ->joinWith(['lesson' => function ($query) use ($locationId,$currentDate) {
            $query->location($locationId)
                ->isConfirmed()
                ->orderBy(['lesson.id' => SORT_ASC])
                ->notCanceled()
                ->andWhere(['>=', 'DATE(lesson.date)', $currentDate])
                ->notDeleted();
            }])
            ->joinWith(['enrolment' => function ($query) use ($customerIds) {
            $query->joinWith(['student' => function ($query) {
                    $query->notDeleted();
                }
                    ])
                    ->notDeleted()
                    ->isConfirmed()
                    ->customer($customerIds);
            }])
            ->notDeleted();

        $dataProvider = new ActiveDataProvider([
            'query' => $lessonQuery,
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $lessonQuery->andFilterWhere(['>=', 'lesson.date', $this->goToDate]);

        return $dataProvider;
    }
}
