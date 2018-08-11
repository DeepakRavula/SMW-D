<?php

use yii\db\Migration;
use common\models\User;
use common\models\Lesson;
use common\models\discount\LessonDiscount;
use common\models\discount\InvoiceLineItemDiscount;

/**
 * Class m180809_074748_exploded_lessons_discount_fix
 */
class m180809_074748_exploded_lessons_discount_fix extends Migration
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
        $explodedLessons = Lesson::find()
            ->notDeleted()
            ->privateLessons()
            ->isConfirmed()
            ->location([14, 15])
            ->split()
            ->notCanceled()
            ->all();
        
        foreach ($explodedLessons as $explodedLesson) {
            $rootLesson = $explodedLesson->rootLesson;
            if ($rootLesson->paymentCycleLesson) {
                if ($rootLesson->lastProFormaLineItem) {
                    $lastProFormaLineItem = $rootLesson->lastProFormaLineItem;
                    if ($rootLesson->lastProFormaLineItem->discounts) {
                        foreach ($explodedLesson->discounts as $exLediscount) {
                            $exLediscount->delete();
                        }
                    }
                    foreach ($lastProFormaLineItem->discounts as $pfliDiscount) {
                        $lessonDiscount = new LessonDiscount();
                        $lessonDiscount->type = $pfliDiscount->type;
                        if (!$pfliDiscount->valueType) {
                            $lessonDiscount->value = $pfliDiscount->value / ($rootLesson->durationSec / Lesson::DEFAULT_EXPLODE_DURATION_SEC);
                        } else {
                            $lessonDiscount->value = $pfliDiscount->value;
                        }
                        $lessonDiscount->valueType = $pfliDiscount->valueType;
                        $lessonDiscount->lessonId = $explodedLesson->id;
                        $lessonDiscount->save();
                    }
                }
            }
        }
        foreach ($explodedLessons as $explodedLesson) {
            if ($explodedLesson->hasInvoice()) {
                foreach ($explodedLesson->invoice->lineItem->discounts as $liDiscount) {
                    $liDiscount->delete();
                }
                
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180809_074748_exploded_lessons_discount_fix cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180809_074748_exploded_lessons_discount_fix cannot be reverted.\n";

        return false;
    }
    */
}
