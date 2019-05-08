<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\User;
use common\models\GroupLesson;
use common\models\Enrolment;

/**
 * Class m190507_104821_data_fix_php_error_group_lesson_reschedule
 */
class m190507_104821_data_fix_php_error_group_lesson_reschedule extends Migration
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
        $locationIds = [4, 9, 14, 15, 16, 17, 18, 19, 20, 21];
        $lessons = Lesson::find()
            ->joinWith(['course' => function ($query) use($locationIds) {
                $query->isConfirmed()
                ->andWhere(['IN', 'course.locationId', $locationIds])
                ->notDeleted();
        }])
                   ->groupLessons()
                    ->notDeleted()
                    ->notCanceled()
                    ->isConfirmed()
                    ->all();
        foreach ($lessons as $lesson) {
            if (!$lesson->groupLesson && $lesson->course->enrolments) {
                $enrolments = Enrolment::find()
                ->joinWith(['course' => function ($query) {
                    $query->isConfirmed()
                        ->notDeleted();
                }])
                ->andWhere(['courseId' => $lesson->course->id])
                ->notDeleted()
                ->isConfirmed()
                ->all();
                if ($enrolments) {
                foreach ($enrolments as $enrolment) {
                    $groupLessonModel = new GroupLesson();
                    $groupLessonModel->lessonId = $lesson->id;
                    $groupLessonModel->enrolmentId = $enrolment->id;
                    $groupLessonModel->save();
                }
              
            } 
                
            
           }
        }  
        die;            
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190507_104821_data_fix_php_error_group_lesson_reschedule cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190507_104821_data_fix_php_error_group_lesson_reschedule cannot be reverted.\n";

        return false;
    }
    */
}
