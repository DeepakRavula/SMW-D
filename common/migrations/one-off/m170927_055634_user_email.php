<?php

use yii\db\Migration;
use common\models\User;
use common\models\UserEmail;

class m170927_055634_user_email extends Migration
{
    public function up()
    {
        $users = User::find()
                ->where(['NOT', ['email' => '']])
                ->andWhere(['NOT', ['email' => null]])
                ->all();
        foreach ($users as $user) {
            $userEmail = new UserEmail();
            $userEmail->userId = $user->id;
            $userEmail->email = $user->email;
            $userEmail->labelId = 1;
            $userEmail->isPrimary = 1;
            $userEmail->save();
        }
        $this->dropColumn('user', 'email');
    }

    public function down()
    {
        echo "m170927_055634_user_email cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
