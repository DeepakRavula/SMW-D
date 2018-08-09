<?php

use yii\db\Migration;
use common\models\Lesson;

/**
 * Class m180809_074748_exploded_lessons_discount_fix
 */
class m180809_074748_exploded_lessons_discount_fix extends Migration
{
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
            foreach ($explodedLesson->discounts as $discount) {
                if ($explodedLesson->rootLesson->discounts) {
                    $rootLessonDiscount = $explodedLesson->rootLesson->discounts;
                } else if ($explodedLesson->rootLesson->paymentCycleLesson) {
                    if ($explodedLesson->rootLesson->lastProFormaLineItem) {
                        if ($explodedLesson->rootLesson->lastProFormaLineItem->discounts) {
                            $rootLessonDiscount = $explodedLesson->rootLesson->lastProFormaLineItem->discounts;
                        }
                    }
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
