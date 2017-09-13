<?php

namespace common\models;

use Yii;
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
        $actualLessonDate            = \DateTime::createFromFormat('Y-m-d H:i:s',
                $this->date);
        $invoiceLineItem->unit       = $this->unit;
        if ($invoice->isProFormaInvoice()) {
            if ($this->isExtra()) {
                $invoiceLineItem->item_type_id = ItemType::TYPE_EXTRA_LESSON;
            } else if ($this->isPrivate()) {
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
        $amount = $this->enrolment->program->rate * $invoiceLineItem->unit;
        $invoiceLineItem->amount       = $amount;
        $studentFullName               = $this->enrolment->student->fullName;
        $description                  = $this->enrolment->program->name.' for '.$studentFullName.' with '
            . $this->teacher->publicIdentity.' on '.$actualLessonDate->format('M. jS, Y');
        $invoiceLineItem->description = $description;
        $invoiceLineItem->code       = $invoiceLineItem->getItemCode();
        if ($invoiceLineItem->save()) {
            $invoiceLineItem->addLineItemDetails($this);
            return $invoiceLineItem;
        } else {
            Yii::error('Create Invoice Line Item: ' . \yii\helpers\VarDumper::dumpAsString($invoiceLineItem->getErrors()));
        }
    }

    public function addGroupLessonLineItem($invoice)
    {
        $invoiceLineItem               = $this->addLessonLineItem($invoice);
        $invoiceLineItem->item_type_id = ItemType::TYPE_GROUP_LESSON;
        $invoiceLineItem->unit         = $this->unit;
        $actualLessonDate              = \DateTime::createFromFormat('Y-m-d H:i:s',
                $this->date);
        $enrolment                     = Enrolment::findOne($this->enrolmentId);
        $courseCount                   = $enrolment->courseCount;
        $lessonAmount                  = $this->course->program->rate / $courseCount;
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
            Yii::error('Create Invoice Line Item: ' . \yii\helpers\VarDumper::dumpAsString($invoiceLineItem->getErrors()));
        } else {
            $invoiceLineItem->addLineItemDetails($this);
            $invoiceItemLesson                    = new InvoiceItemEnrolment();
            $invoiceItemLesson->enrolmentId       = $enrolment->id;
            $invoiceItemLesson->invoiceLineItemId = $invoiceLineItem->id;
            $invoiceItemLesson->save();
            return $invoiceLineItem;
        }
    }

    public function addGroupProFormaLineItem($enrolment, $invoice)
    {
        $invoiceLineItem = $this->addLessonLineItem($invoice);
        $invoiceLineItem->item_type_id = ItemType::TYPE_GROUP_LESSON;
        $courseCount = $enrolment->courseCount;
        $invoiceLineItem->unit       = $enrolment->firstLesson->unit * $courseCount;
        $qualification = Qualification::findOne(['teacher_id' => $enrolment->firstLesson->teacherId,
            'program_id' => $enrolment->course->program->id]);
        $rate = !empty($qualification->rate) ? $qualification->rate : 0;
        $invoiceLineItem->cost       = $rate;
        $invoiceLineItem->rate = $rate;
        $invoiceLineItem->amount = $enrolment->program->rate;
        $studentFullName = $enrolment->student->fullName;
        $invoiceLineItem->description  = $enrolment->program->name . ' for '. $studentFullName . ' with '
            . $enrolment->firstLesson->teacher->publicIdentity;
        $invoiceLineItem->code = $invoiceLineItem->getItemCode();
        if ($invoiceLineItem->save()) {
            $invoiceLineItem->addLineItemDetails($enrolment);
            return $invoiceLineItem;
        } else {
            Yii::error('Create Invoice Line Item: ' . \yii\helpers\VarDumper::dumpAsString($invoiceLineItem->getErrors()));
        }
    }
}
