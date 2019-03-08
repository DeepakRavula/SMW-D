<?php

namespace common\models;

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
}
