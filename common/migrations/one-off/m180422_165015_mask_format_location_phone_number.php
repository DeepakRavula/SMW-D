<?php

use yii\db\Migration;
use common\models\Location;
/**
 * Class m180422_165015_mask_format_location_phone_number
 */
class m180422_165015_mask_format_location_phone_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
		$locations = Location::find()->all();
		foreach ($locations as $location) {
			if ($location->phone_number[3] == "-") {
				$phone_number = preg_replace("/[^A-Za-z0-9]/", "", $location->phone_number);
			}
			if (is_numeric($phone_number)) {
				$newPhoneNumber = substr_replace($phone_number, "(", 0) . substr_replace($phone_number, ")", 3) . " " . substr_replace(substr($phone_number, 3), "-", 3) . substr($phone_number, 6);
				$location->updateAttributes([
				    'phone_number' => $newPhoneNumber,
				]);
			}
		}
	}

	/**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180422_165015_mask_format_location_phone_number cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180422_165015_mask_format_location_phone_number cannot be reverted.\n";

        return false;
    }
    */
}
