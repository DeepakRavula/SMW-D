<?php

use yii\db\Migration;
use common\models\User;
use common\models\Location;

/**
 * Class m221121_072549_nobleton_cron_enable
 */
class m221121_072549_nobleton_cron_enable extends Migration
{
    /**
     * {@inheritdoc}
     */
    const LOCATION_ID = 22;
    public function init()
    {
        parent::init();
        $user = User::findByRole(User::ROLE_BOT);
        $botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));


    }
    public function safeUp()
    {
        $location = Location::findOne(['id' => self::LOCATION_ID]); //nobleton location
        $location->updateAttributes(['isEnabledCron' => true]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221121_072549_nobleton_cron_enable cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221121_072549_nobleton_cron_enable cannot be reverted.\n";

        return false;
    }
    */
}
