<?php

namespace common\models\query;

use common\models\Invoice;

/**
 * This is the ActiveQuery class for [[Invoice]].
 *
 * @see Invoice
 */
class InvoiceQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     *
     * @return Invoice[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return Invoice|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

	public function notDeleted()
    {
        $this->andWhere(['invoice.isDeleted' => false]);

        return $this;
    }
	
	public function location($locationId)
    {
        $this->andWhere(['invoice.location_id' => $locationId]);
        return $this;
    }
	
    public function student($id)
    {
        $this->joinWith(['lineItems' => function ($query) use ($id) {
            $query->joinWith(['lesson' => function ($query) use ($id) {
                $query->joinWith(['enrolment' => function ($query) use ($id) {
                    $query->joinWith('student')
						->andWhere(['student.id' => $id]);
                }]);
            }]);
        }]);

        return $this;
    }

    public function enrolmentLesson($lessonId, $enrolmentId)
    {
        return $this->joinWith(['lineItems' => function($query) use ($lessonId, $enrolmentId) {
            $query->joinWith(['lineItemLesson' => function($query) use ($lessonId, $enrolmentId) {
                $query->joinWith(['lineItemEnrolment' => function($query) use ($enrolmentId) {
                    $query->andWhere(['invoice_item_enrolment.enrolmentId' => $enrolmentId]);
                }]);
                $query->where(['invoice_item_lesson.lessonId' => $lessonId]);
            }]);
        }]);
    }

    public function invoiceCredit($userId)
    {
        $this->where([
            'user_id' => $userId,
            'type' => Invoice::TYPE_INVOICE,
        ])
        ->andWhere(['<', 'balance', 0]);

        return $this;
    }

    public function proFormaCredit($lessonId)
    {
        $this->joinWith(['lineItems' => function ($query) use ($lessonId) {
            $query->where(['item_id' => $lessonId]);
        }])
            ->joinWith(['invoicePayments' => function ($query) {
                $query->joinWith('payment');
            }])
            ->where(['invoice.type' => Invoice::TYPE_PRO_FORMA_INVOICE]);

        return $this;
    }

    public function pendingInvoices($enrolmentId, $model)
    {
        $this->joinWith(['lineItems li' => function ($query) use ($enrolmentId, $model) {
            $query->joinWith(['lesson l' => function ($query) use ($enrolmentId, $model) {
                $query->joinWith(['enrolment' => function ($query) use ($enrolmentId, $model) {
                    $query->joinWith('student')
                        ->where(['student.customer_id' => $model->customer->id, 'student.id' => $model->id]);
                }])
                    ->where(['enrolment.id' => $enrolmentId]);
            }]);
        }]);

        return $this;
    }

    public function proFormaInvoiceCredits($customerId)
    {
        $this->select(['i.id', 'i.date', 'SUM(p.amount) as credit'])
            ->joinWith(['invoicePayments ip' => function ($query) use ($customerId) {
                $query->joinWith(['payment p' => function ($query) use ($customerId) {
                }]);
            }])
            ->where(['i.type' => Invoice::TYPE_PRO_FORMA_INVOICE, 'i.user_id' => $customerId])
            ->groupBy('i.id');

        return $this;
    }

	public function unpaid()
	{
		return $this->andFilterWhere([
			'invoice.status' => Invoice::STATUS_OWING,
		]);
	}

	public function proFormaInvoice()
	{
		return $this->andFilterWhere([
			'type' => Invoice::TYPE_PRO_FORMA_INVOICE
		]);
	}

	public function invoice()
	{
		return $this->andFilterWhere([
			'type' => Invoice::TYPE_INVOICE
		]);
	}
	
	public function paid()
	{
		return $this->andFilterWhere([
			'invoice.status' => Invoice::STATUS_PAID,
		]);
	}

	public function mailSent()
	{
		return $this->andFilterWhere([
			'isSent' => true
		]);
	}

	public function mailNotSent()
	{
		return $this->andFilterWhere([
			'isSent' => false
		]);
	}

	public function between($fromDate, $toDate)
    {
        return $this->andFilterWhere(['between', 'invoice.date', $fromDate, $toDate]);
    }
}
