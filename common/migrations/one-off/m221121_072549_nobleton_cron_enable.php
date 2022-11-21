<?php

use yii\db\Migration;
use common\models\Location;

/**
 * Class m221121_072549_nobleton_cron_enable
 */
class m221121_072549_nobleton_cron_enable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $location = Location::findOne(['id' => 22]); //nobleton location
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
