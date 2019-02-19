<?php

use yii\db\Migration;
use common\models\Lesson;
use yii\helpers\Console;
use common\models\Location;

/**
 * Class m190214_062142_add_status_copy
 */
class m190214_062142_add_status_copy extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        
        Console::startProgress(0, 'Rounding lessons to two decimal places...');    
        $locations = Location::find()->all();
        foreach ($locations as $location) {
            $lessons = Lesson::find()
                ->isConfirmed()
                ->location($location->id)
                ->privateLessons()
                ->notCanceled()
                ->notDeleted()
                ->joinWith(['lessonPayment' => function ($query) {
                    $query->andWhere(['NOT', ['lesson_payment.id' => null]]);
                }])
                ->all();
            
            foreach ($lessons as $lesson) {
                Console::output("processing: " . $lesson->id . 'rounded to two decimal place', Console::FG_GREEN, Console::BOLD);
                $status = Lesson::STATUS_PAID;
                if ($lesson->hasCredit($lesson->enrolment->id)) {
                    $status = Lesson::STATUS_CREDIT;
                }
                if ($lesson->isOwing($lesson->enrolment->id)) {
                    $status = Lesson::STATUS_OWING;
                }
                $lesson->updateAttributes([
                    'paidStatus' => $status,
                    'total' => $lesson->netPrice
                ]);
            }
        }
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190214_062142_add_status_copy cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190214_062142_add_status_copy cannot be reverted.\n";

        return false;
    }
    */
}
