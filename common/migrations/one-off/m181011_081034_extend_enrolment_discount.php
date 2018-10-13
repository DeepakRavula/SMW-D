<?php

use yii\db\Migration;
use common\models\LessonOwing;
use common\models\Enrolment;
/**
 * Class m181011_081034_extend_enrolment_discount
 */
class m181011_081034_extend_enrolment_discount extends Migration
{
    /**
     * {@inheritdoc}
     */
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
                    $lessonOwing = new LessonOwing();
                    $lessonOwing->lessonId = $lesson->id;
                    $lessonOwing->save();
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
