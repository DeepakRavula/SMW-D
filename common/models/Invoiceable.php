<?php

namespace common\models;

use Yii;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "invoice".
 *
 * @property int $id
 * @property int $lesson_id
 * @property int $type
 * @property string $amount
 * @property string $date
 * @property int $status
 */
trait Invoiceable
{
    public function addLessonLineItem($invoice)
    {
        $invoiceLineItem             = new InvoiceLineItem();
        $invoiceLineItem->invoice_id = $invoice->id;
        $item = Item::findOne(['code' => Item::LESSON_ITEM]);
        $invoiceLineItem->item_id    = $item->id;

        return $invoiceLineItem;
    }

    public function addPrivateLessonLineItem($invoice)
    {
        $invoiceLineItem = $this->addLessonLineItem($invoice);
        $qualification = Qualification::findOne(['teacher_id' => $this->teacherId,
            'program_id' => $this->course->program->id]);
        $rate = !empty($qualification->rate) ? $qualification->rate : 0;
        $actualLessonDate            = \DateTime::createFromFormat(
            'Y-m-d H:i:s',
                $this->date
        );
        if ($this->proFormaLineItem) {
            $invoiceLineItem->amount = $this->proFormaLineItem->amount;
            $invoiceLineItem->unit   = $this->proFormaLineItem->unit;
        } else {
            $invoiceLineItem->amount = $this->courseProgramRate ? $this->courseProgramRate->programRate
                    : $this->enrolment->program->rate;
            $invoiceLineItem->unit   = $this->unit;
        }
        if ($invoice->isProFormaInvoice()) {
            if ($this->isExtra()) {
                $invoiceLineItem->item_type_id = ItemType::TYPE_EXTRA_LESSON;
            } elseif ($this->isPrivate()) {
                $invoiceLineItem->item_type_id = ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON;
            }
            $invoiceLineItem->cost       = $rate * $invoiceLineItem->unit;
        } else {
            if ($this->isUnscheduled()) {
                $invoiceLineItem->cost       = 0;
            } else {
                $invoiceLineItem->cost       = $rate * $invoiceLineItem->unit;
            }
            $invoiceLineItem->item_type_id = ItemType::TYPE_PRIVATE_LESSON;
            $invoiceLineItem->rate = $rate;
        }
        $studentFullName               = $this->enrolment->student->fullName;
        $description                  = $this->enrolment->program->name.' for '.$studentFullName.' with '
            . $this->teacher->publicIdentity.' on '.$actualLessonDate->format('M. jS, Y');
        $invoiceLineItem->description = $description;
        $invoiceLineItem->code       = $invoiceLineItem->getItemCode();
        if ($invoiceLineItem->save()) {
            $invoiceLineItem->addLineItemDetails($this);
            return $invoiceLineItem;
        } else {
            Yii::error('Create Invoice Line Item: ' . VarDumper::dumpAsString($invoiceLineItem->getErrors()));
        }
    }

    public function addGroupLessonLineItem($invoice)
    {
        $invoiceLineItem               = $this->addLessonLineItem($invoice);
        $invoiceLineItem->item_type_id = ItemType::TYPE_GROUP_LESSON;
        $invoiceLineItem->unit         = $this->unit;
        $actualLessonDate              = \DateTime::createFromFormat(
            'Y-m-d H:i:s',
                $this->date
        );
        $enrolment                     = Enrolment::findOne($this->enrolmentId);
        if ($enrolment->isExtra()) {
            $courseCount = 1;
        } else {
            $courseCount = $enrolment->courseCount;
        }
        $lessonAmount = $enrolment->courseProgramRate->programRate / $courseCount;
        $qualification = Qualification::findOne(['teacher_id' => $enrolment->firstLesson->teacherId,
            'program_id' => $enrolment->course->program->id]);
        $rate = !empty($qualification->rate) ? $qualification->rate : 0;
        $invoiceLineItem->cost       = $rate;
        $invoiceLineItem->rate = $rate;
        $invoiceLineItem->amount       = $lessonAmount;
        $studentFullName               = $enrolment->student->fullName;
        $description                   = $enrolment->program->name . ' for '. $studentFullName . ' with '
            . $this->teacher->publicIdentity . ' on ' . $actualLessonDate->format('M. jS, Y');
        $invoiceLineItem->description  = $description;
        $invoiceLineItem->item_type_id = ItemType::TYPE_GROUP_LESSON;
        $invoiceLineItem->code         = $invoiceLineItem->getItemCode();
        if (!$invoiceLineItem->save()) {
            Yii::error('Create Invoice Line Item: ' . VarDumper::dumpAsString($invoiceLineItem->getErrors()));
        } else {
            $invoiceItemLesson                    = new InvoiceItemEnrolment();
            $invoiceItemLesson->enrolmentId       = $enrolment->id;
            $invoiceItemLesson->invoiceLineItemId = $invoiceLineItem->id;
            $invoiceItemLesson->save();
            if ($this->courseProgramRate->applyFullDiscount) {
                $invoiceLineItem->addFullDiscount();
            }
            $invoiceLineItem->addLineItemDetails($this);
            return $invoiceLineItem;
        }
    }

    public function createInvoice()
    {
        $invoice = new Invoice();
        $invoice->on(Invoice::EVENT_CREATE, [new InvoiceLog(), 'create']);
        $invoice->type = INVOICE::TYPE_INVOICE;
        return $invoice;
    }
    
    public function createPrivateLessonInvoice()
    {
        $invoice = $this->createInvoice();
        $location_id = $this->enrolment->student->customer->userLocation->location_id;
        if (is_a(Yii::$app, 'yii\console\Application')) {
            $roleUser = User::findByRole(User::ROLE_BOT);
            $botUser = end($roleUser);
            $loggedUser = User::findOne(['id' => $botUser->id]);
        } else {
            $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
        }
        $invoice->userName = $loggedUser->userProfile->fullName;
        $invoice->user_id = $this->enrolment->student->customer->id;
        $invoice->location_id = $location_id;
        $invoice->save();
        $this->addPrivateLessonLineItem($invoice);
        $invoice->save();
        if ($this->hasLessonCredit($this->enrolment->id)) {
            $invoice->addPayment($this, $this->getLessonCreditAmount($this->enrolment->id), $this->enrolment);
        }
        if (!empty($this->extendedLessons)) {
            foreach ($this->extendedLessons as $extendedLesson) {
                $lineItem = $extendedLesson->lesson->addPrivateLessonLineItem($invoice);
                $invoice->save();
                if ($extendedLesson->lesson->hasLessonCredit($this->enrolment->id)) {
                    $amount = $extendedLesson->lesson->getSplitedAmount();
                    if ($amount > $extendedLesson->lesson->getLessonCreditAmount($this->enrolment->id)) {
                        $amount = $extendedLesson->lesson->getLessonCreditAmount($this->enrolment->id);
                    }
                    $invoice->addPayment($extendedLesson->lesson, $amount, $this->enrolment);
                }
            }
        }

        return $invoice;
    }

    public function createGroupInvoice($enrolmentId)
    {
        $invoice   = $this->createInvoice();
        $enrolment = Enrolment::findOne($enrolmentId);
        $location_id = $enrolment->student->customer->userLocation->location_id;
        $user = User::findOne(['id' => $enrolment->student->customer->id]);
        $invoice->userName = $user->publicIdentity;
        $invoice->user_id = $enrolment->student->customer->id;
        $invoice->location_id = $location_id;
        $invoice->save();
        $this->enrolmentId = $enrolmentId;
        $this->addGroupLessonLineItem($invoice);
        $invoice->save();
        if ($this->hasLessonCredit($enrolmentId)) {
            $netPrice = $this->getLessonCreditAmount($enrolmentId);
            $invoice->addPayment($this, $netPrice, $enrolment);
        }

        return $invoice;
    }
    
    public function addCreditInvoice($startDate = null, $endDate)
    {
        $endDate = (new \DateTime($endDate))->format('Y-m-d');
        $query = Lesson::find()
                    ->notDeleted()
                    ->isConfirmed();
        if ($startDate) {
            $startDate = (new \DateTime($startDate))->format('Y-m-d');
            $query->andWhere(['AND', ['>=', 'DATE(date)', $startDate], ['<=', 'DATE(date)', $endDate]]);
        } else {
            $query->andWhere(['>=', 'DATE(date)', $endDate]);
        }
        $lessons = $query->andWhere(['courseId' => $this->courseId])
                    ->all();
        $hasCredit = false;
        foreach ($lessons as $lesson) {
            if ($lesson->hasLessonCredit($this->id)) {
                $hasCredit = true;
            }
        }
        if ($hasCredit) {
            $invoice = $this->addLessonCreditInvoice();
        }
        foreach ($lessons as $lesson) {
            if ($lesson->hasLessonCredit($this->id)) {
                if ($hasCredit) {
                    $invoice->save();
                }
                $invoice->addPayment($lesson, $lesson->getLessonCreditAmount($this->id), $this);
            }
            $lesson->Cancel();
            $lesson->delete();
        }
        $paymentCycleQuery = PaymentCycle::find()
                ->where(['enrolmentId' => $this->id]);
        if ($startDate) {
            $paymentCycleQuery->andWhere(['OR', ['between', "DATE(endDate)", $startDate, $endDate],
                                ['between', "DATE(startDate)", $startDate, $endDate]]);
        } else {
            $paymentCycleQuery->andWhere(['OR',
                ['AND', ['<', 'DATE(startDate)', $endDate], ['>', 'DATE(endDate)', $endDate]],
                ['>', 'DATE(startDate)', $endDate]]);
        }
        $paymentCycles = $paymentCycleQuery->all();
        foreach ($paymentCycles as $paymentCycle) {
            if (!$paymentCycle->hasLessons()) {
                $paymentCycle->delete();
            }
        }
        return $hasCredit ? $invoice : null;
    }
    
    public function createProFormaInvoice()
    {
        $locationId = $this->student->customer->userLocation->location_id;
        $user = User::findOne(['id' => $this->student->customer->id]);
        $invoice = new Invoice();
        $invoice->on(Invoice::EVENT_CREATE, [new InvoiceLog(), 'create']);
        if (is_a(Yii::$app, 'yii\console\Application')) {
            $roleUser = User::findByRole(User::ROLE_BOT);
            $botUser = end($roleUser);
            $loggedUser = User::findOne(['id' => $botUser->id]);
        } else {
            $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
        }
        $invoice->userName = $loggedUser->userProfile->fullName;
        $invoice->user_id = $user->id;
        $invoice->location_id = $locationId;
        $invoice->dueDate = (new \DateTime($this->firstLesson->date))->format('Y-m-d');
        $invoice->type = INVOICE::TYPE_PRO_FORMA_INVOICE;
        $invoice->createdUserId = Yii::$app->user->id;
        $invoice->updatedUserId = Yii::$app->user->id;
        if (!$invoice->save()) {
            Yii::error('Create Invoice: ' . VarDumper::dumpAsString($invoice->getErrors()));
        }
        foreach ($this->lessons as $lesson) {
            $lesson->enrolmentId = $this->id;
            $lesson->addGroupLessonLineItem($invoice);
        }
        if (!$invoice->save()) {
            Yii::error('Create Invoice: ' . VarDumper::dumpAsString($invoice->getErrors()));
        }
        return $invoice;
    }
    
    public function addLessonCreditInvoice()
    {
        $invoice = new Invoice();
        $invoice->user_id = $this->customer->id;
        $invoice->location_id = $this->customer->userLocation->location_id;
        $invoice->type = Invoice::TYPE_INVOICE;
        $invoice->save();
        $invoiceLineItem = new InvoiceLineItem(['scenario' => InvoiceLineItem::SCENARIO_OPENING_BALANCE]);
        $invoiceLineItem->invoice_id = $invoice->id;
        $item = Item::findOne(['code' => Item::LESSON_CREDIT]);
        $invoiceLineItem->item_id = $item->id;
        $invoiceLineItem->item_type_id = ItemType::TYPE_LESSON_CREDIT;
        $invoiceLineItem->description = $this->student->studentIdentity .'\'s '
                . $this->course->program->name . ' Lesson credit';
        $invoiceLineItem->unit = 1;
        $invoiceLineItem->amount = 0.0;
        $invoiceLineItem->code = $invoiceLineItem->getItemCode();
        $invoiceLineItem->cost = 0;
        $invoiceLineItem->save();
        $invoice->tax = $invoiceLineItem->tax_rate;
        $invoice->total = $invoice->subTotal + $invoice->tax;
        $invoice->date = (new \DateTime())->format('Y-m-d H:i:s');
        return $invoice;
    }
}
