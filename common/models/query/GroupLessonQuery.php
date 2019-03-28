<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[GroupLesson]].
 *
 * @see GroupLesson
 */
class GroupLessonQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return GroupLesson[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return GroupLesson|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function dueLessons()
    {
        return $this->andFilterWhere(['<', 'group_lesson.dueDate', (new \DateTime())->format('Y-m-d H:i:s')]);
    }

    public function dueBetween($fromDate, $toDate)
    {
        return $this->andFilterWhere(['between', 'group_lesson.dueDate', $fromDate->format('Y-m-d'), $toDate->format('Y-m-d')]);
    }

    public function enrolment($enrolmentId) 
    {
        return $this->andFilterWhere(['enrolmentId' => $enrolmentId]);
    }
}
