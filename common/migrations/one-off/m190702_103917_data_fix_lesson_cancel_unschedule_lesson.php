<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\User;
use common\models\Location;
use yii\helpers\Console;
/**
 * Class m190702_103917_data_fix_lesson_cancel_unschedule_lesson
 */
class m190702_103917_data_fix_lesson_cancel_unschedule_lesson extends Migration
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
        $locationIds = [1, 4, 9, 14, 15, 16, 17, 18, 19, 20, 21];
        Console::startProgress(0, 100, 'Cancelling lessons');
        $lessonCount = 0;
        $locations = Location::find()->andWhere(['id' => $locationIds])->all();
        foreach ($locations as $location) {
            $lessons = Lesson::find()
            ->isConfirmed()
            ->notCanceled()
            ->notDeleted()
            ->location([$location->id])
            ->all();
            foreach ($lessons as $lesson) {
                if ($lesson->getLeaf() && $lesson->status !== Lesson::STATUS_CANCELED) {
                    Console::output("Affected Lesson: " . $lesson->id, Console::FG_GREEN, Console::BOLD);
                    $lesson->updateAttributes(['status' => Lesson::STATUS_CANCELED]);
                    $lessonCount++;
                }
            }
          
        }
        Console::output("Affected Lessons Count".$lessonCount, Console::FG_GREEN, Console::BOLD);
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190702_103917_data_fix_lesson_cancel_unschedule_lesson cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190702_103917_data_fix_lesson_cancel_unschedule_lesson cannot be reverted.\n";

        return false;
    }
    */
}
