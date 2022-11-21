<?php

use yii\db\Migration;
use common\models\User;
use common\models\UserLocation;

/**
 * Class m180814_074024_fix_user_without_location
 */
class m180814_074024_fix_user_without_location extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $users = User::find()
            ->notAdmin()
            ->joinWith(['userLocation' => function($query) {
                $query->andWhere(['user_location.id' => null]);
            }])
            ->joinWith(['invoice' => function($query) {
                $query->andWhere(['NOT', ['invoice.id' => null]]);
            }])
            ->all();
        foreach ($users as $user) {
            $userLocation = new UserLocation();
            $userLocation->user_id = $user->id;
            $userLocation->location_id = $user->invoice->location_id;
            $userLocation->save();
        }
        $users = User::find()
            ->notAdmin()
            ->joinWith(['userLocation' => function($query) {
                $query->andWhere(['user_location.id' => null]);
            }])
            ->joinWith(['allStudents' => function($query) {
                $query->andWhere(['NOT', ['student.id' => null]])
                ->joinWith(['enrolment' => function($query) {
                    $query->andWhere(['NOT', ['enrolment.id' => null]])
                    ->joinWith(['course' => function($query) {
                        $query->andWhere(['NOT', ['course.id' => null]]);
                    }]);
                }]);
            }])
            ->all();
            
        foreach ($users as $user) {
            $locationId = null;
            $userLocation = new UserLocation();
            $userLocation->user_id = $user->id;
            foreach ($user->allStudents as $student) {
                foreach ($student->enrolment as $enrolment) {
                    if ($enrolment->course) {
                        $locationId = $enrolment->course->locationId;
                    }
                }
            }
            if ($locationId) {
                $userLocation->location_id = $locationId;
                $userLocation->save();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180814_074024_fix_user_without_location cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180814_074024_fix_user_without_location cannot be reverted.\n";

        return false;
    }
    */
}
