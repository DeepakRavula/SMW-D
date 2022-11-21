<?php

use common\models\User;
use yii\db\Migration;
use common\models\Location;
use common\models\Student;
use common\models\UserProfile;
use yii\helpers\Console;

/**
 * Class m191012_053604_adding_missed_user_profile
 */
class m191012_053604_adding_missed_user_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $locationIds = [1, 4, 9, 14, 15, 16, 17, 18, 19, 20, 21];
        Console::startProgress(0, 100, 'Cancelling lessons');
        $lessonCount = 0;
        $locations = Location::find()->andWhere(['id' => $locationIds])->all();
        foreach ($locations as $location) {
            $users = User::find()
                ->joinWith(['userProfile' => function ($query) {
                    $query->andWhere(['user_profile.user_id' => null]);
                }])
                ->notDeleted()
                ->location([$location->id])
                ->all();
            foreach ($users as $user) {
                    $student = Student::find()
                        ->andWhere(['customer_id' => $user->id])
                        ->one();
                     if ($student) {    
                    $userProfile = new UserProfile();
                    $userProfile->firstname = $student->first_name;
                    $userProfile->lastname = $student->first_name;
                    $userProfile->user_id = $user->id;
                    $userProfile->save();
                    Console::output("Affected User" . $user->id, Console::FG_GREEN, Console::BOLD);
            }
        }
    }
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191012_053604_adding_missed_user_profile cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191012_053604_adding_missed_user_profile cannot be reverted.\n";

        return false;
    }
    */
}
