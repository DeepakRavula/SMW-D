<?php

use yii\db\Migration;
use common\models\discount\LessonDiscount;
use common\models\Enrolment;
use common\models\User;
use common\models\discount\EnrolmentDiscount;

/**
 * Class m181011_081034_extend_enrolment_discount
 */
class m181011_081034_fix_extend_enrolment_discount extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $user = User::findByRole(User::ROLE_BOT);
        $botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }

    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $enrolments = Enrolment::find()
                    ->joinWith(['enrolmentDiscount' => function ($query) {
                        $query->andWhere(['NOT', [ 'OR', ['enrolment_discount.id' => null], ['enrolment_discount.discount' => NULL]]]);
                    }])
                    ->location([14,15])
                    ->notDeleted()
                    ->isConfirmed()
                   ->all();
       foreach($enrolments as $enrolment) {
            foreach($enrolment->lessons as $lesson){
                if (!$lesson->lessonDiscount) {
                    if ($lesson->grossPrice == $lesson->netPrice && $lesson->getOwingAmount($lesson->enrolment->id) > 0) {
                        if ($lesson->enrolment->enrolmentDiscount) {
                            foreach($lesson->enrolment->enrolmentDiscount as $enrolmentDiscount) {
                                $lessonDiscount = new LessonDiscount();
                                $lessonDiscount->lessonId = $lesson->id;
                                $lessonDiscount->value = $enrolmentDiscount->discount;
                                if ($enrolmentDiscount->discountType === EnrolmentDiscount::VALUE_TYPE_PERCENTAGE) {
                                    $lessonDiscount->valueType = LessonDiscount::VALUE_TYPE_PERCENTAGE;
                                }
                                else {
                                    $lessonDiscount->valueType = LessonDiscount::VALUE_TYPE_DOLLAR;
                                }
                                $lessonDiscount->type = $enrolmentDiscount->type;
                                $lessonDiscount->save();
                            }
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
        echo "m181011_081034_extend_enrolment_discount cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181011_081034_extend_enrolment_discount cannot be reverted.\n";

        return false;
    }
    */
}
