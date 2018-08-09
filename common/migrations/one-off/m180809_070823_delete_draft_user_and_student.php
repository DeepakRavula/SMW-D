<?php

use yii\db\Migration;
use common\models\User;
use common\models\Student;

/**
 * Class m180809_070823_delete_draft_user_and_student
 */
class m180809_070823_delete_draft_user_and_student extends Migration
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
        $users = User::find()
            ->notDeleted()
            ->draft()
            ->all();

        foreach ($users as $user) {
            $user->delete();
        }

        $students = Student::find()
            ->notDeleted()
            ->draft()
            ->all();

        foreach ($students as $student) {
            $student->delete();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180809_070823_delete_draft_user_and_student cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180809_070823_delete_draft_user_and_student cannot be reverted.\n";

        return false;
    }
    */
}
