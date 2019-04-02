<?php

namespace common\models\query;

use common\models\CustomerRecurringPaymentEnrolment;

/**
 * This is the ActiveQuery class for [[InvoiceLineItem]].
 *
 * @see InvoiceLineItem
 */
class CustomerRecurringPaymentEnrolmentQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     *
     * @return CustomerRecurringPaymentEnrolment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return CustomerRecurringPaymentEnrolment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
