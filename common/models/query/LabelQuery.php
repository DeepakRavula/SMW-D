<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\Lesson]].
 *
 * @see \common\models\Lesson
 */
class LabelQuery extends \yii\db\ActiveQuery
{
    public function user($id)
    {
        return $this->andWhere(['OR', ['userAdded' => [$id, 0]]]);
    }
}
