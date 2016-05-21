<?php

namespace common\models\query;

use common\models\Lesson;

/**
 * This is the ActiveQuery class for [[Lesson]].
 *
 * @see Lesson
 */
class LessonQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Lesson[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Lesson|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return $this
     */
    public function unInvoiced()
    {
        $this->leftJoin('invoice', 'invoice.lesson_id = lesson.id')
			->where(['invoice.id' => null]);
        return $this;
    }
}
