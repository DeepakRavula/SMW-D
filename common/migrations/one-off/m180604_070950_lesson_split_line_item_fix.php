<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\PaymentCycleLesson;
use common\models\InvoiceItemPaymentCycleLesson;
use common\models\User;

/**
 * Class m180604_070950_lesson_split_line_item_fix
 */
class m180604_070950_lesson_split_line_item_fix extends Migration
{
    public function init() 
    {
        parent::init();
		$user = User::findByRole(User::ROLE_BOT);
		$botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $lessons = Lesson::find()
            ->location([15, 14])
            ->notCanceled()
            ->isConfirmed()
            ->unmergedSplit()
            ->all();
        foreach ($lessons as $lesson) {
            if ($lesson->hasRootLesson()) {
                $rootLesson = $lesson->rootLesson;
                if (!$lesson->hasProFormaInvoice() && $rootLesson->hasProFormaInvoice()) {
                    $lesson->addPrivateLessonLineItem($rootLesson->proFormaInvoice);
                } else if ($lesson->hasProFormaInvoice()) {
                    $invoice = $lesson->proFormaInvoice;
                    $lesson->proformaLineItem->delete();
                    $lesson->addPrivateLessonLineItem($invoice);
                }
            }
        }
        foreach ($lessons as $lesson) {
            if ($lesson->hasRootLesson()) {
                $rootLesson = $lesson->rootLesson;
                if ($rootLesson->hasProFormaInvoice()) {
                    $rootLesson->proformaLineItem->delete();
                }
            }
        }
        $deletedlessons = Lesson::find()
            ->location([15, 14])
            ->notCanceled()
            ->deleted()
            ->isConfirmed()
            ->unmergedSplit()
            ->all();
        foreach ($deletedlessons as $deletedlesson) {
            if ($deletedlesson->hasProFormaInvoice()) {
                $deletedlesson->proformaLineItem->delete();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180604_070950_lesson_split_line_item_fix cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180604_070950_lesson_split_line_item_fix cannot be reverted.\n";

        return false;
    }
    */
}
