<?php

use yii\db\Migration;
use yii\helpers\Console;
use common\models\Lesson;
use common\models\Enrolment;
use common\models\Location;
use Carbon\Carbon;

/**
 * Class m190806_103448_changing_duedate_to_fifteen
 */
class m190806_103448_changing_duedate_to_fifteen extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $locationIds = [1, 4, 9, 13, 14, 15, 16, 17, 18, 19, 20, 21];
        Console::startProgress(0, 100, 'Making lesson due dates to fifteen of every month!!!');
        $locations = Location::find()->andWhere(['id' => $locationIds])->all();
        foreach ($locations as $location) {
        Console::startProgress(0, 'Processing Enrolments');
        $enrolments = Enrolment::find()
                       ->location($location->id) 
                       ->isConfirmed()
                       ->notDeleted()
                       ->isRegular()
                       ->privateProgram()
                       ->all();
        foreach ($enrolments as $enrolment){
            Console::output("processing: " . $enrolment->id . 'processing', Console::FG_GREEN, Console::BOLD);
           $this->setDueDate($enrolment->id);
        }  
    }
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);             
        return true;

    }

    public function setDueDate($enrolmentId)
    {
        $lessons = Lesson::find()
            ->enrolment($enrolmentId)
            ->isConfirmed()
            ->notCanceled()
            ->notDeleted()
            ->andWhere(['>', 'DATE(lesson.date)' , Carbon::now()->format('Y-m-d')])
            ->all();
        foreach ($lessons as $lesson){
            if ($lesson->paymentCycle) {
                $dueDateMonth = Carbon::parse($lesson->dueDate)->format('m');
                $dueDateYear = Carbon::parse($lesson->dueDate)->format('Y');
                $formatedDate = 15 . '-' . $dueDateMonth . '-' . $dueDateYear;
                $dueDate = Carbon::parse($formatedDate)->format('Y-m-d');
                $lesson->updateAttributes(['dueDate' => $dueDate]); 
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190806_103448_changing_duedate_to_fifteen cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190806_103448_changing_duedate_to_fifteen cannot be reverted.\n";

        return false;
    }
    */
}
