<?php

use yii\db\Migration;
use common\models\User;
use common\models\Course;
use common\models\UserLocation;

/**
 * Class m180817_090853_user_location_fix
 */
class m180817_090853_user_location_fix extends Migration
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
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $courses = Course::find()
            ->isConfirmed()
            ->all();
        
        foreach ($courses as $course) {
            foreach ($course->enrolments as $enrolment) {
                if (!$enrolment->student->customer->userLocation) {
                    $userLocation = new UserLocation();
                    $userLocation->user_id = $enrolment->student->customer->id;
                    $userLocation->location_id = $course->locationId;
                    $userLocation->save();
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180817_090853_user_location_fix cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180817_090853_user_location_fix cannot be reverted.\n";

        return false;
    }
    */
}
