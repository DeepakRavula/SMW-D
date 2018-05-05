<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\PaymentCycleLesson]].
 *
 * @see \common\models\PaymentCycleLesson
 */
class PaymentCycleLessonQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\PaymentCycleLesson[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\PaymentCycleLesson|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function notDeleted()
    {
        return $this->andWhere(['payment_cycle_lesson.isDeleted' => false]);
    }

    public function deleted()
    {
        return $this->andWhere(['payment_cycle_lesson.isDeleted' => true]);
    }
}
