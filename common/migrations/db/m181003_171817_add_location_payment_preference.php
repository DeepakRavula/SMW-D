<?php

use yii\db\Migration;
use common\models\Location;
use common\models\User;
use common\models\LocationPaymentPreference;

/**
 * Class m181003_171817_add_location_payment_preference
 */
class m181003_171817_add_location_payment_preference extends Migration
{
    public function init() 
    {
        parent::init();
        $user = User::findByRole(User::ROLE_BOT);
        $botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('location_payment_preference');
        if ($tableSchema == null) {
            $this->createTable('location_payment_preference', [
                'id' => $this->primaryKey(),
                'locationId' => $this->integer()->notNull(),
                'isPreferredPaymentEnabled' => $this->boolean()->notNull(),
                'createdOn' => $this->timeStamp()->defaultValue(null),
                'updatedOn' => $this->timeStamp()->defaultValue(null),
                'createdByUserId' =>  $this->integer()->notNull(),
                'updatedByUserId' =>  $this->integer()->notNull(),
            ]);
        }
        $locations = Location::find()
                ->notDeleted()
                ->all();
            foreach ($locations as $location) {
                $locationPaymentPreference = new LocationPaymentPreference();
                $locationPaymentPreference->locationId = $location->id;
                if ($location->isEnabledCron == true) {
                    $locationPaymentPreference->isPreferredPaymentEnabled = true;
                } 
                $locationPaymentPreference->save();
            }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181003_171817_add_location_payment_preference cannot be reverted.\n";

        return false;
    }
}
