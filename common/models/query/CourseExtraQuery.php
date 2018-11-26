<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\CourseExtra]].
 *
 * @see \common\models\CourseExtra
 */
class CourseExtraQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\CourseExtra[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\CourseExtra|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function notDeleted() {
        return $this->andWhere(['course_extra.isDeleted' => false]);
    }
}
