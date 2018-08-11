<?php

use yii\db\Migration;
use common\models\Student;
use common\models\User;
use common\models\UserLocation;

/**
 * Class m180810_060717_fix_draft_students
 */
class m180810_060717_fix_draft_students extends Migration
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
        $studentIds = [2162, 2520, 2524, 2525, 2534, 2535, 2536, 2537, 2538, 2540, 2548, 2570 , 2573 , 2580 , 2581 , 2596 , 2604 , 2631];
        $students = Student::find()
            ->where(['id' => $studentIds])
            ->all();
        foreach ($students as $student) {
            if ($student->customer->userLocation) {
                $userLocation = new UserLocation();
                $userLocation->location_id = $student->oneEnrolment->course->locationId;
                $userLocation->user_id = $student->customer->id;
                $userLocation->save();
            }
            $student->customer->updateAttributes([
                'isDeleted' => false,
                'status' => User::STATUS_ACTIVE
            ]);
            $student->updateAttributes([
                'isDeleted' => false,
                'status' => Student::STATUS_ACTIVE
            ]);
        }

        $users = User::find()
            ->draft()
            ->all();

        foreach ($users as $user) {
            $user->updateAttributes(['status' => User::STATUS_NOT_ACTIVE, 'isDeleted' => true]);
        }

        $students = Student::find()
            ->draft()
            ->all();

        foreach ($students as $student) {
            $student->updateAttributes(['status' => Student::STATUS_INACTIVE, 'isDeleted' => true]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180810_060717_fix_draft_students cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180810_060717_fix_draft_students cannot be reverted.\n";

        return false;
    }
    */
}
