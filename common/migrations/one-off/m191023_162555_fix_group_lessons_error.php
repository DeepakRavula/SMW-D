<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\GroupLesson;
use common\models\User;
/**
 * Class m191023_162555_fix_group_lessons_error
 */
class m191023_162555_fix_group_lessons_error extends Migration
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
        
        $query = Lesson::find()
                ->isConfirmed()
                ->notDeleted()
                ->location([1,4,9,14,15,16,17,18,19,20,21]);
        $groupLessons = $query->groupLessons()->completed()->all();
        foreach ($groupLessons as $lesson) {
            foreach ($lesson->enrolments as $enrolment) {
             $groupLesson = GroupLesson::findOne(['lessonId' => $lesson->id, 'enrolmentId' => $enrolment->id]);
             if(!$groupLesson) {
                $groupLessonModel = new GroupLesson();
                $groupLessonModel->dueDate = $lesson->dueDate;
                $groupLessonModel->enrolmentId = $enrolment->id;
                $groupLessonModel->isDeleted = false;
                $groupLessonModel->lessonId = $lesson->id;
                $groupLessonModel->save();
             }

            }
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191023_162555_fix_group_lessons_error cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191023_162555_fix_group_lessons_error cannot be reverted.\n";

        return false;
    }
    */
}
