<?php

namespace common\models\query;

use common\models\Payment;
use common\models\Invoice;
use yii\db\ActiveQuery;

/**
 * Class PaymentQuery.
 *
 * @author Eugene Terentev <eugene@terentev.net>
 */
class PaymentQuery extends ActiveQuery
{
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return Payment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
    /**
     * @return $this
     */
    public function openingBalance($invoice)
    {
        $this->where(['payment.user_id' => $invoice->user_id])
            ->andWhere(['like', 'payment.amount', '-']);

        return $this;
    }

    public function proFormaInvoice()
    {
        $this->joinWith(['invoicePayment ip' => function ($query) {
            $query->joinWith(['invoice i' => function ($query) {
                $query->where(['i.type' => Invoice::TYPE_PRO_FORMA_INVOICE]);
            }]);
        }]);

        return $this;
    }

	public function location($locationId) {
		$this->joinWith('invoice')
			->where(['location_id' => $locationId]);
		return $this;
	}
}
