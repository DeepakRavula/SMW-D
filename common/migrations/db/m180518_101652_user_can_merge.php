<?php

use yii\db\Migration;
use common\models\User;

/**
 * Class m180518_101652_user_can_merge
 */
class m180518_101652_user_can_merge extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'canMerge', $this->boolean()->notNull()->after('canLogin'));
        $users = User::find()
            ->notDeleted()
            ->admin()
            ->all();
        foreach ($users as $user) {
            $user->updateAttributes(['canMerge' => true]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180518_101652_user_can_merge cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180518_101652_user_can_merge cannot be reverted.\n";

        return false;
    }
    */
}
