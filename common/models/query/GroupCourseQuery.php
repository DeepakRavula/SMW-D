<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\GroupCourse]].
 *
 * @see \common\models\GroupCourse
 */
class GroupCourseQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\GroupCourse[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\GroupCourse|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
