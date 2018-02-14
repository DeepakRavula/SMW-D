<?php

use yii\db\Migration;
use common\models\UserLocation;
use common\models\UserAddress;
use common\models\UserContact;
use common\models\UserPhone;
use common\models\UserEmail;
use common\models\UserToken;

/**
 * Class m180213_102018_wipe_deleted_users_details
 */
class m180213_102018_wipe_deleted_users_details extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $userLocations = UserLocation::find()
                ->joinWith(['user u' => function ($query) {
                    $query->andWhere(['u.id' => null]);
                }])
                ->all();
        foreach ($userLocations as $userLocation) {
            $userLocation->delete();
        }
        $userTokens = UserToken::find()
                ->joinWith(['user u' => function ($query) {
                    $query->andWhere(['u.id' => null]);
                }])
                ->all();
        foreach ($userTokens as $userToken) {
            $userToken->delete();
        }
        $userPhones = UserPhone::find()
                ->joinWith(['userContact uc' => function ($query) {
                    $query->joinWith(['user u' => function ($query) {
                        $query->andWhere(['u.id' => null]);
                    }]);
                }])
                ->all();
        foreach ($userPhones as $userPhone) {
            $userPhone->delete();
        }
        $userEmails = UserEmail::find()
                ->joinWith(['userContact uc' => function ($query) {
                    $query->joinWith(['user u' => function ($query) {
                        $query->andWhere(['u.id' => null]);
                    }]);
                }])
                ->all();
        foreach ($userEmails as $userEmail) {
            $userEmail->delete();
        }
        $userAddresses = UserAddress::find()
                ->joinWith(['userContact uc' => function ($query) {
                    $query->joinWith(['user u' => function ($query) {
                        $query->andWhere(['u.id' => null]);
                    }]);
                }])
                ->all();
        foreach ($userAddresses as $userAddress) {
            $userAddress->delete();
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180213_102018_wipe_deleted_users_details cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180213_102018_wipe_deleted_users_details cannot be reverted.\n";

        return false;
    }
    */
}
