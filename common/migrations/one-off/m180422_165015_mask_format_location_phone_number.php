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
		$phoneNumbers = Location::find()->all();
		foreach ($phoneNumbers as $phoneNumber) {
			if ($phoneNumber->phone_number[3] == "-") {
				$phone_numbers = preg_replace("/[^A-Za-z0-9]/", "", $phoneNumber->phone_number);
			}
			if (is_numeric($phone_numbers)) {
				
				//print_r('nvnvnvn');
				$newPhoneNumber = substr_replace($phone_numbers, "(", 0) . substr_replace($phone_numbers, ")", 3) . " " . substr_replace(substr($phone_numbers, 3), "-", 3) . substr($phone_numbers, 6);
				//print_r($newPhoneNumber);die();
				$phoneNumber->phone_number = $newPhoneNumber;
				print_r($phoneNumber->phone_number);die();
				$phoneNumber->save();
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
