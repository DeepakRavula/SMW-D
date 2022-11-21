<?php

use yii\db\Migration;
use common\models\User;

/**
 * Class m181025_100313_enable_customer_payment_preference_richmond_hill
 */
class m181025_100313_enable_customer_payment_preference_richmond_hill extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $customers = User::find()
           ->joinWith(['customerPaymentPreference' => function ($query) {
               $query ->andWhere(['NOT', ['customer_payment_preference.id' => null]]);
              
           }])
           ->location([16])
           ->notDeleted()
           ->all();
        foreach($customers as $customer) {
           $customer->customerPaymentPreference->updateAttributes([
            'isPreferredPaymentEnabled' => 1
        ]);

        }   

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181025_100313_enable_customer_payment_preference_richmond_hill cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181025_100313_enable_customer_payment_preference_richmond_hill cannot be reverted.\n";

        return false;
    }
    */
}
