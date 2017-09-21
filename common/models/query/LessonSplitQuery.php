<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\PrivateLesson]].
 *
 * @see \common\models\PrivateLesson
 */
class LessonSplitQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     *
     * @return \common\models\PrivateLesson[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\PrivateLesson|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function unUsedSplits($courseId, $locationId)
    {
        return $this->joinWith(['lesson' => function ($query) use ($locationId, $courseId) {
                        $query->joinWith(['lessonReschedule' => function($query) {
                            $query->andWhere(['lesson_reschedule.lessonId' => null]);
                        }]);
                        $query->location($locationId)
                            ->andWhere(['lesson.courseId' => $courseId]);
                    }])
                    ->joinWith(['privateLesson' => function ($query) {
                        $query->isNotExpired();
                    }])
                    ->joinWith('lessonSplitUsage')
                    ->andWhere(['lesson_split_usage.lessonSplitId' => null])
                    ->orderBy(['private_lesson.expiryDate' => SORT_ASC])
                    ->groupBy(['lesson_split.lessonId']);
    }
}
