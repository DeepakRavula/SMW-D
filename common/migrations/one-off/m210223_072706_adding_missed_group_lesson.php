<?php

use common\models\GroupLesson;
use common\models\Lesson;
use common\models\User;
use yii\db\Migration;

/**
 * Class m210223_072706_adding_missed_group_lesson
 */
class m210223_072706_adding_missed_group_lesson extends Migration
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
        $lessonIds = [1585438,1585437,1585436,1585435];
        foreach ($lessonIds as $lessonId) {
        $lesson = Lesson::findOne($lessonId);
        $groupLesson = GroupLesson::find()
        ->andWhere(['lessonId' => $lessonId])
        ->andWhere(['enrolmentId' => 12660])->one();
        if (!$groupLesson){
            $groupLesson = new GroupLesson();
            $groupLesson->lessonId = $lessonId;
            $groupLesson->enrolmentId = 12660;
            $groupLesson->dueDate = (new \DateTime($lesson->date))->format('Y-m-d');
            $groupLesson->save(); 
        }   else {
            echo "lesson".$lessonId." is already present";
        }
    }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210223_072706_adding_missed_group_lesson cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210223_072706_adding_missed_group_lesson cannot be reverted.\n";

        return false;
    }
    */
}
