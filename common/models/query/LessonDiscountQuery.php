<?php

namespace common\models\query;

use common\models\discount\LessonDiscount;


/**
 * This is the ActiveQuery class for [[\common\models\LessonDiscount]].
 *
 * @see \common\models\LessonDiscount
 */
class LessonDiscountQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\LessonDiscount[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\LessonDiscount|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function customerDiscount()
    {
        return $this->andWhere(['lesson_discount.type' => LessonDiscount::TYPE_CUSTOMER]);
    }

    public function paymentFrequencyDiscount()
    {
        return $this->andWhere(['lesson_discount.type' => LessonDiscount::TYPE_ENROLMENT_PAYMENT_FREQUENCY]);
    }

    public function multiEnrolmentDiscount()
    {
        return $this->andWhere(['lesson_discount.type' => LessonDiscount::TYPE_MULTIPLE_ENROLMENT]);
    }

    public function lineItemDiscount()
    {
        return $this->andWhere(['lesson_discount.type' => LessonDiscount::TYPE_LINE_ITEM]);
    }
}
