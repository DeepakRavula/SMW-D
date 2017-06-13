<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\InvoiceItemPaymentCycleLesson]].
 *
 * @see \common\models\InvoiceItemPaymentCycleLesson
 */
class InvoiceItemPaymentCycleLessonQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\InvoiceItemPaymentCycleLesson[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\InvoiceItemPaymentCycleLesson|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
