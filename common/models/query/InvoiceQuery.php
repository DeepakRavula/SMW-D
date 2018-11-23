<?php

namespace common\models\query;

use common\models\Invoice;
use common\models\ItemType;

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

    public function deleted()
    {
        return $this->andWhere(['invoice.isDeleted' => true]);
    }

    public function notCanceled()
    {
        return $this->andWhere(['invoice.isCanceled' => false]);
    }

    public function canceled()
    {
        return $this->andWhere(['invoice.isCanceled' => true]);
    }

    public function notReturned()
    {
        return $this->joinWith(['reverseInvoice' => function ($query) {
            $query->andWhere(['invoice_reverse.id' => null]);
        }]);
    }

    public function returned()
    {
        return $this->joinWith(['reverseInvoice' => function ($query) {
            $query->andWhere(['NOT', ['invoice_reverse.id' => null]]);
        }]);
    }

    public function location($locationId)
    {
        return $this->andWhere(['invoice.location_id' => $locationId]);
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
        return $this->joinWith(['lineItems' => function ($query) use ($lessonId, $enrolmentId) {
            $query->joinWith(['lineItemLesson' => function ($query) use ($lessonId, $enrolmentId) {
                $query->joinWith(['lineItemEnrolment' => function ($query) use ($enrolmentId) {
                    $query->andWhere(['invoice_item_enrolment.enrolmentId' => $enrolmentId]);
                }]);
                $query->andWhere(['invoice_item_lesson.lessonId' => $lessonId]);
            }]);
        }]);
    }

    public function invoiceCredit($userId)
    {
        return $this->andWhere(['user_id' => $userId])
            ->andWhere(['<', 'round(balance, 2)', -0.1]);
    }

    public function proFormaCredit($lessonId)
    {
        $this->joinWith(['lineItems' => function ($query) use ($lessonId) {
            $query->andWhere(['item_id' => $lessonId]);
        }])
            ->joinWith(['invoicePayments' => function ($query) {
                $query->joinWith('payment');
            }])
            ->andWhere(['invoice.type' => Invoice::TYPE_PRO_FORMA_INVOICE]);

        return $this;
    }

    public function pendingInvoices($enrolmentId, $model)
    {
        $this->joinWith(['lineItems li' => function ($query) use ($enrolmentId, $model) {
            $query->joinWith(['lesson l' => function ($query) use ($enrolmentId, $model) {
                $query->joinWith(['enrolment' => function ($query) use ($enrolmentId, $model) {
                    $query->joinWith('student')
                        ->andWhere(['student.customer_id' => $model->customer->id, 'student.id' => $model->id]);
                }])
                    ->andWhere(['enrolment.id' => $enrolmentId]);
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
            ->andWhere(['i.type' => Invoice::TYPE_PRO_FORMA_INVOICE, 'i.user_id' => $customerId])
            ->groupBy('i.id');

        return $this;
    }

    public function manualPayments()
    {
        return $this->joinWith(['invoicePayments' => function ($query) {
            $query->joinWith(['payment' => function ($query) {
                $query->notDeleted()
                ->exceptAutoPayments();
            }]);
        }]);
    }

    public function appliedPayments()
    {
        return $this->joinWith(['invoicePayments' => function ($query) {
            $query->joinWith(['payment' => function ($query) {
                $query->notDeleted()
                    ->notCreditUsed();
            }]);
        }]);
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
            'invoice.type' => Invoice::TYPE_PRO_FORMA_INVOICE
        ]);
    }

    public function invoice()
    {
        return $this->andFilterWhere([
            'invoice.type' => Invoice::TYPE_INVOICE
        ]);
    }

    public function lessonInvoice()
    {
        return $this->invoice()
            ->joinWith(['lineItem' => function ($query) {
            $query->andWhere(['invoice_line_item.item_type_id' => [ItemType::TYPE_PRIVATE_LESSON, ItemType::TYPE_EXTRA_LESSON]]);
        }]);
    }
    
    public function paid()
    {
        return $this->andFilterWhere([
            'invoice.status' => Invoice::STATUS_PAID,
        ]);
    }

    public function credit()
    {
        return $this->andFilterWhere([
            'invoice.status' => Invoice::STATUS_CREDIT,
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
        return $this->andFilterWhere(['between', 'DATE(invoice.date)', $fromDate, $toDate]);
    }

    public function nonPfi()
    {
        return $this->joinWith(['proformaInvoiceItem' => function ($query) {
            $query->joinWith(['proformaLineItem' => function ($query) {
                $query->joinWith(['proformaInvoice' => function ($query) {
                    $query->andWhere(['proforma_invoice.id' => null]);
                }]);
            }]);
        }]);
    }

    public function customer($customerId)
    {
        return $this->andWhere(['invoice.user_id' => $customerId]);
    }
    
    public function openingBalance()
    {
        return $this->joinWith(['lineItem' => function ($query) {
            $query->andWhere(['invoice_line_item.item_type_id' => ItemType::TYPE_OPENING_BALANCE]);
        }]);
    }

    public function nonLessonCredit()
    {
        return $this->joinWith(['lineItem' => function ($query) {
            $query->andWhere(['NOT', ['invoice_line_item.item_type_id' => ItemType::TYPE_LESSON_CREDIT]]);
        }]);
    }

    public function lessonCredit()
    {
        return $this->joinWith(['lineItem' => function ($query) {
            $query->andWhere(['invoice_line_item.item_type_id' => ItemType::TYPE_LESSON_CREDIT]);
        }]);
    }

    public function lessonCreditUsed()
    {
        return $this->joinWith(['invoicePayments' => function ($query) {
            $query->joinWith(['payment' => function ($query) {
                $query->notDeleted()
                    ->lessonCreditUsed();
            }]);
        }]);
    }

    public function userLocation($locationId)
    {
        $this->joinWith(['user' => function ($query) use ($locationId) {
            $query->joinWith('userProfile up')
                 ->joinWith(['userLocation' => function ($query) use ($locationId) {
                     $query->andWhere(['user_location.location_id' => $locationId]);
                 }]);
        }]);
        return $this;
    }
}
