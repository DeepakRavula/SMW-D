<?php

use yii\db\Migration;
use common\models\User;
use common\models\UserLocation;

/**
 * Class m180723_111641_walkin_customer_location
 */
class m180723_111641_walkin_customer_location extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $users = User::find()
            ->notDeleted()
            ->guests()
            ->all();
        foreach ($users as $user) {
            if (!$user->hasLocation()) {
                if ($user->hasInvoice()) {
                    $userLocation = new UserLocation();
                    $userLocation->user_id = $user->id;
                    $userLocation->location_id = $user->invoice->location_id;
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
        echo "m180723_111641_walkin_customer_location cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180723_111641_walkin_customer_location cannot be reverted.\n";

        return false;
    }
    */
}
