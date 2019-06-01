<?php

namespace common\models;

use Yii;
use yii\helpers\VarDumper;
use common\models\log\InvoiceLog;
use common\models\User;
use common\models\log\LessonLog;
use common\models\Lesson;
use common\models\discount\LessonDiscount;

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
        $rate = $this->teacherRate;
        $actualLessonDate            = \DateTime::createFromFormat(
            'Y-m-d H:i:s',
                $this->date
        );
        $invoiceLineItem->amount = $this->programRate;
        $invoiceLineItem->unit   = $this->unit;
        $invoiceLineItem->unit   = $this->unit;
        if ($this->isUnscheduled()) {
            $invoiceLineItem->cost       = 0;
        } else {
            $invoiceLineItem->cost       = $rate * $invoiceLineItem->unit;
        }
        $invoiceLineItem->item_type_id = ItemType::TYPE_PRIVATE_LESSON;
        $invoiceLineItem->rate = $rate;
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
        $lessonAmount = $this->programRate;
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
            if (!$this->isPrivate()) {
                $groupDiscount = LessonDiscount::find()
                    ->groupDiscount()
                    ->andWhere(['lessonId' => $this->id, 'enrolmentId' => $enrolment->id])
                    ->one();
                if ($groupDiscount) {
                    $invoiceLineItem->addGroupDiscount($groupDiscount);
                }
            }
            return $invoiceLineItem;
        }
    }

    public function revertLessonsCredit($lessons)
    {
        foreach ($lessons as $lesson) {
            if (!$lesson->hasInvoice()) {
                $lesson->cancel();
                $lesson->delete();
            }
        }
        return true;
    }

    public function revertGroupLessonsCredit($lessons)
    {
        foreach ($lessons as $lesson) {
            if (!$lesson->hasInvoice()) {
                $lesson->cancel();
                $lesson->delete();
            }
        }
        return true;
    }

    public function createInvoice()
    {
        $invoice = new Invoice();
        $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
        $invoice->on(Invoice::EVENT_AFTER_INSERT, [new InvoiceLog(), 'addInvoice'], ['loggedUser' => $loggedUser]);
        $invoice->type = INVOICE::TYPE_INVOICE;
        return $invoice;
    }
    
    public function createPrivateLessonInvoice()
    {        
        if ($this->hasInvoice()) {
            return $this->invoice;
        }
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
        if ($this->isExpired()) {
            $invoice->date = (new \DateTime($this->privateLesson->expiryDate))->format('Y-m-d H:i:s'); 
        } else {
            $invoice->date = (new \DateTime($this->date))->format('Y-m-d H:i:s');
        }
        $invoice->save();
        $this->addPrivateLessonLineItem($invoice);
        $invoice->save();       
        $this->creditTransfer($invoice);
        if ($invoice->lineItem->lesson) {
            $lesson = $invoice->lineItem->lesson;
            if ($lesson->isExpired()) {
                $lesson->on(Lesson::EVENT_LESSON_EXPIRED, [new LessonLog(), 'lessonExpired'], ['loggedUser' => $loggedUser]);
                $lesson->trigger(Lesson::EVENT_LESSON_EXPIRED);
            }
            $lesson->on(Lesson::EVENT_CREATE_INVOICE, [new LessonLog(), 'addInvoice'], ['loggedUser' => $loggedUser]);
            $lesson->trigger(Lesson::EVENT_CREATE_INVOICE);
            }
        return $invoice;
    }

    public function creditTransfer($invoice)
    {
        $payment = new Payment();
        if ($this->hasLessonCredit($this->enrolment->id)) {
            $payment->amount = $this->getLessonCreditAmount($this->enrolment->id);
            $invoice->addPayment($this, $payment, $this->enrolment);
        }
        if (!empty($this->usedLessonSplits)) {
            foreach ($this->usedLessonSplits as $extendedLesson) {
                $invoice->save();
                if ($extendedLesson->lesson->hasLessonCredit($this->enrolment->id)) {
                    $amount = $extendedLesson->lesson->getSplitedAmount();
                    if ($amount > $extendedLesson->lesson->getLessonCreditAmount($this->enrolment->id)) {
                        $amount = $extendedLesson->lesson->getLessonCreditAmount($this->enrolment->id);
                    }
                    $payment->amount = $amount;
                    $invoice->addPayment($extendedLesson->lesson, $payment, $this->enrolment);
                }
            }
        }
        return true;
    }

    public function createGroupInvoice($enrolmentId)
    {
        $enrolment = Enrolment::findOne($enrolmentId);
        if ($enrolment->hasInvoice($this->id)) {
            return $enrolment->getInvoice($this->id);
        }
        $invoice   = $this->createInvoice();
        $location_id = $enrolment->student->customer->userLocation->location_id;
        $user = User::findOne(['id' => $enrolment->student->customer->id]);
        $invoice->userName = $user->publicIdentity;
        $invoice->user_id = $enrolment->student->customer->id;
        $invoice->location_id = $location_id;
        $invoice->date = (new \DateTime($this->date))->format('Y-m-d H:i:s');
        $invoice->save();
        $this->enrolmentId = $enrolmentId;
        $this->addGroupLessonLineItem($invoice);
        $invoice->save();
        if ($this->hasLessonCredit($enrolmentId)) {
            $payment = new Payment();
            $payment->amount = $this->getLessonCreditAmount($enrolmentId);
            $invoice->addPayment($this, $payment, $enrolment);
        }

        return $invoice;
    }
    
    public function addCreditInvoice($endDate)
    {
        $endDate = (new \DateTime($endDate))->format('Y-m-d');
        $lessons = Lesson::find()
            ->notDeleted()
            ->notCanceled()
            ->isConfirmed()
            ->andWhere(['lesson.courseId' => $this->courseId])
            ->andWhere(['>', 'DATE(lesson.date)', $endDate])
            ->all();
        foreach ($lessons as $lesson) {
            if (!$lesson->hasInvoice()) {
                $lesson->cancel();
                $lesson->delete();
            }
        }
        return true;
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

    public function createPaymentRequest($dateRange)
    {
        list($from_date, $to_date) = explode(' - ', $dateRange);
        $fromDate = new \DateTime($from_date);
        $toDate = new \DateTime($to_date);
        $invoicedLessons = Lesson::find()
            ->notDeleted()
            ->isConfirmed()
            ->notCanceled()
            ->privateLessons()
            ->between($fromDate, $toDate)
            ->enrolment($this->id)
            ->invoiced();
        $lessons = Lesson::find()   
            ->notDeleted()
            ->isConfirmed()
            ->notCanceled()
            ->privateLessons()
            ->between($fromDate, $toDate)
            ->enrolment($this->id)
            ->joinWith(['privateLesson' => function($query) {
                $query->andWhere(['>', 'private_lesson.balance', 0]);
            }])
            ->leftJoin(['invoiced_lesson' => $invoicedLessons], 'lesson.id = invoiced_lesson.id')
            ->andWhere(['invoiced_lesson.id' => null])
            ->orderBy(['lesson.date' => SORT_ASC])
            ->all();
        if ($lessons) {
            $firstLesson = current($lessons);
            if (!$firstLesson->hasAutomatedPaymentRequest()) {
                $model = new ProformaInvoice();
                $model->userId = $this->customer->id;
                $model->locationId = $this->customer->userLocation->location_id;
                $model->proforma_invoice_number = $model->getProformaInvoiceNumber();
                $model->save();
                foreach ($lessons as $lesson) {
                    $proformaLineItem = new ProformaLineItem();
                    $proformaLineItem->proformaInvoiceId = $model->id;
                    $proformaLineItem->lessonId = $lesson->id;
                    $proformaLineItem->save();
                }
                $model->save();
            }
        }
        return true;
    }
}
