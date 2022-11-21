<?php

use yii\db\Migration;
use common\models\User;

class m180125_073512_add_can_login extends Migration
{
    public function up()
    {
        $this->addColumn(
            'user',
            'canLogin',
            $this->boolean()->notNull()->after('status')
        );
        $users = User::find()->all();
        foreach ($users as $user) {
            if (!$user->isStaff()) {
                $canLogin = true;
            } else {
                $canLogin = false;
            }
            $user->updateAttributes([
                'canLogin' => $canLogin
            ]);
        }
    }

    public function down()
    {
        echo "m180125_073512_add_can_login cannot be reverted.\n";

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
