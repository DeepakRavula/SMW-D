<?php

use yii\db\Migration;
use common\models\Location;

/**
 * Class m180917_065828_disable_automatic_payments_for_location
 */
class m180917_065828_disable_automatic_payments_for_location extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->addColumn('location', 'isEnabledCron', $this->boolean()->notNull());

        $productionLocationIds = [14,15,16];
        foreach ($productionLocationIds as $productionLocationId) {
            $productionLocation       = Location::find()->andWhere(['id' => $productionLocationId])->one();
            $productionLocation->updateAttributes(['isEnabledCron' => true]);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180917_065828_disable_automatic_payments_for_location cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180917_065828_disable_automatic_payments_for_location cannot be reverted.\n";

        return false;
    }
    */
}
