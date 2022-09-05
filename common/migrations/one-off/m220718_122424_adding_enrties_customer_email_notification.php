<?php

use yii\db\Migration;
use common\models\CustomerEmailNotification;
use common\models\NotificationEmailType;
use common\models\User;
use common\models\Location;

/**
 * Class m220718_122424_adding_enrties_customer_email_notification
 */
class m220718_122424_adding_enrties_customer_email_notification extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $locations = Location::find()->all();
        foreach($locations as $location) {

            $users = User::find()->customers($location->id)->all();

            foreach ($users as $user) {
                $emailNotifyTypes = NotificationEmailType::find()->all();

                foreach($emailNotifyTypes as $emailNotifyType){
                    $customerEmailNotification = new CustomerEmailNotification();
                    $customerEmailNotification->userId = $user->id;
                    $customerEmailNotification->emailNotificationTypeId = $emailNotifyType->id;
                    $customerEmailNotification->isChecked = false;
                    $customerEmailNotification->save();
                }
                
            }

        }
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220718_122424_adding_enrties_customer_email_notification cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220718_122424_adding_enrties_customer_email_notification cannot be reverted.\n";

        return false;
    }
    */
}