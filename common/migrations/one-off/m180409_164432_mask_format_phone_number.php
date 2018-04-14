<?php

use yii\db\Migration;
use common\models\UserPhone;
/**
 * Class m180409_164432_mask_format_phone_number
 */
class m180409_164432_mask_format_phone_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	$phoneNumbers = UserPhone::find()->all();
	foreach ($phoneNumbers as $phoneNumber) {
			if ($phoneNumber->number[3] == "-") {
				$phone_number = preg_replace("/[^A-Za-z0-9]/", "", $phoneNumber->number);
			} else {
				$phone_number = $phoneNumber->number;
			}
			if (is_numeric($phone_number)) {
				$newPhoneNumber = substr_replace($phone_number, "(", 0) . substr_replace($phone_number, ")", 3) . " " . substr_replace(substr($phone_number, 3), "-", 3) . substr($phone_number, 6);
				$phoneNumber->number = $newPhoneNumber;
				$phoneNumber->save();
			}
		}
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180409_164432_mask_format_phone_number cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180409_164432_mask_format_phone_number cannot be reverted.\n";

        return false;
    }
    */
}
