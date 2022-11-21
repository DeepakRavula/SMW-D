<?php

use yii\db\Migration;
use common\models\CustomerReferralSource;

/**
 * Class m180831_070352_delete_customerreferralsource_with_null
 */
class m180831_070352_delete_customerreferralsource_with_null extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $customerReferralSources = CustomerReferralSource::find()->andWhere(['referralSourceId'=>NULL])->all();
        foreach ($customerReferralSources as $customerReferralSource) {
            $customerReferralSource->delete();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180831_070352_delete_customerreferralsource_with_null cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180831_070352_delete_customerreferralsource_with_null cannot be reverted.\n";

        return false;
    }
    */
}
