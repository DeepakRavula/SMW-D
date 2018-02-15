<?php

use yii\db\Migration;
use common\models\User;

/**
 * Class m180214_111307_set_primary_email
 */
class m180214_111307_set_primary_email extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $users = User::find()
                ->notDeleted()
                ->all();
        foreach ($users as $user) {
            if (!$user->hasPrimaryEmail()) {
                if ($user->emails) {
                    current($user->emails)->makePrimary();
                    continue;
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180214_111307_set_primary_email cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180214_111307_set_primary_email cannot be reverted.\n";

        return false;
    }
    */
}
