<?php

use yii\db\Migration;
use common\models\User;
use common\models\Lesson;
use common\models\discount\LessonDiscount;
use common\models\discount\InvoiceLineItemDiscount;

/**
 * Class m180809_074748_exploded_lessons_discount_fix
 */
class m180809_074750_exploded_lessons_line_item_discount_fix extends Migration
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
            if ($explodedLesson->hasInvoice()) {
                foreach ($explodedLesson->discounts as $elDiscount) {
                    $lessonDiscount = new InvoiceLineItemDiscount();
                    $lessonDiscount->type = $elDiscount->type;
                    $lessonDiscount->value = $elDiscount->value;
                    $lessonDiscount->valueType = $elDiscount->valueType;
                    $lessonDiscount->invoiceLineItemId = $explodedLesson->invoice->lineItem->id;
                    $lessonDiscount->save();
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
