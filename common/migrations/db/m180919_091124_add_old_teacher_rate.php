<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\User;

/**
 * Class m180919_091124_add_old_teacher_rate
 */
class m180919_091124_add_old_teacher_rate extends Migration
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
        $this->addColumn('lesson', 'teacherRateOld', $this->decimal(10, 4)->notNull());
        $lessons = Lesson::find()
                ->all();
            foreach ($lessons as $lesson) {
                $lesson->teacherRateOld = $lesson->teacherRate;
                $lesson->save();
            }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180919_091124_add_old_teacher_rate cannot be reverted.\n";

        return false;
    }
}
