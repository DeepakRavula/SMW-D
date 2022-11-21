<?php

use yii\db\Migration;
use common\models\UserLocation;

/**
 * Class m190510_142724_remove_duplicate_user_locations
 */
class m190510_142724_remove_duplicate_user_locations extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $userLocationIds = ['2553', '5835', '6302', '6309'];
        $userLocations = UserLocation::find()
                ->andWhere(['id' => $userLocationIds])
                ->all();
        foreach ($userLocations as $userLocation) {
            $userLocation->delete();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190510_142724_remove_duplicate_user_locations cannot be reverted.\n";

        return false;
    }
}
