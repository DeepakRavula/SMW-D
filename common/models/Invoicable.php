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
trait Invoicable
{
    public function addLessonLineItem()
    {
        $invoiceLineItem             = new InvoiceLineItem();
        $invoiceLineItem->invoice_id = $this->id;
        $item = Item::findOne(['code' => Item::LESSON_ITEM]);
        $invoiceLineItem->item_id    = $item->id;

        return $invoiceLineItem;
    }

    public function addPrivateLessonLineItem($lesson)
    {
        $invoiceLineItem = $this->addLessonLineItem();
        $qualification = Qualification::findOne(['teacher_id' => $lesson->teacherId,
            'program_id' => $lesson->course->program->id]);
        $rate = !empty($qualification->rate) ? $qualification->rate : 0;
        $actualLessonDate            = \DateTime::createFromFormat('Y-m-d H:i:s',
                $lesson->date);
        $invoiceLineItem->unit       = $lesson->unit;
        if ($this->isProFormaInvoice()) {
            if ($lesson->isExtra()) {
                $invoiceLineItem->item_type_id = ItemType::TYPE_EXTRA_LESSON;
            } else if ($lesson->isPrivate()) {
                $invoiceLineItem->item_type_id = ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON;
            }
            $invoiceLineItem->cost       = $rate * $invoiceLineItem->unit;
        } else {
            if ($lesson->isUnscheduled()) {
                $invoiceLineItem->cost       = 0;
            } else {
                $invoiceLineItem->cost       = $rate * $invoiceLineItem->unit;
            }
            $invoiceLineItem->item_type_id = ItemType::TYPE_PRIVATE_LESSON;
			$invoiceLineItem->rate = $rate;
        }
        $amount = $lesson->enrolment->program->rate * $invoiceLineItem->unit;
        $invoiceLineItem->amount       = $amount;
        $studentFullName               = $lesson->enrolment->student->fullName;
        $description                  = $lesson->enrolment->program->name.' for '.$studentFullName.' with '
            .$lesson->teacher->publicIdentity.' on '.$actualLessonDate->format('M. jS, Y');
        $invoiceLineItem->description = $description;
        $invoiceLineItem->code       = $invoiceLineItem->getItemCode();
        if ($invoiceLineItem->save()) {
            $invoiceLineItem->addLineItemDetails($lesson);
            return $invoiceLineItem;
        } else {
            Yii::error('Create Invoice Line Item: ' . \yii\helpers\VarDumper::dumpAsString($invoiceLineItem->getErrors()));
        }
    }

    public function addGroupLessonLineItem($lesson)
    {
        $invoiceLineItem               = $this->addLessonLineItem();
        $invoiceLineItem->item_type_id = ItemType::TYPE_GROUP_LESSON;
        $invoiceLineItem->unit         = $lesson->unit;
        $actualLessonDate              = \DateTime::createFromFormat('Y-m-d H:i:s',
                $lesson->date);
        $enrolment                     = Enrolment::findOne($lesson->enrolmentId);
        $courseCount                   = $enrolment->courseCount;
        $lessonAmount                  = $lesson->course->program->rate / $courseCount;
        $qualification = Qualification::findOne(['teacher_id' => $enrolment->firstLesson->teacherId,
            'program_id' => $enrolment->course->program->id]);
        $rate = !empty($qualification->rate) ? $qualification->rate : 0;
        $invoiceLineItem->cost       = $rate;
		$invoiceLineItem->rate = $rate;
        $invoiceLineItem->amount       = $lessonAmount;
        $studentFullName               = $enrolment->student->fullName;
        $description                   = $enrolment->program->name . ' for '. $studentFullName . ' with '
            . $lesson->teacher->publicIdentity . ' on ' . $actualLessonDate->format('M. jS, Y');
        $invoiceLineItem->description  = $description;
        $invoiceLineItem->item_type_id = ItemType::TYPE_GROUP_LESSON;
        $invoiceLineItem->code         = $invoiceLineItem->getItemCode();
        if (!$invoiceLineItem->save()) {
            Yii::error('Create Invoice Line Item: ' . \yii\helpers\VarDumper::dumpAsString($invoiceLineItem->getErrors()));
        } else {
            $invoiceLineItem->addLineItemDetails($lesson);
            $invoiceItemLesson                    = new InvoiceItemEnrolment();
            $invoiceItemLesson->enrolmentId       = $enrolment->id;
            $invoiceItemLesson->invoiceLineItemId = $invoiceLineItem->id;
            $invoiceItemLesson->save();
            return $invoiceLineItem;
        }
    }

    public function addGroupProFormaLineItem($enrolment)
    {
        $invoiceLineItem = $this->addLessonLineItem();
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
