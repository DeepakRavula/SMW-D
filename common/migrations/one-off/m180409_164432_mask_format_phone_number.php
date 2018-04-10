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
	foreach($phoneNumbers as $phoneNumber) {
		$phone_number = $phoneNumber->number;
		//print_r($phone_number[3]);die();
		//$slength = strlen($phone_number);
		if($phone_number[3] == "-") {
		$new = preg_replace("/[^A-Za-z0-9]/", "", $phone_number);
//		print_r($new);die();
		
//		print_r($new);
		if(is_numeric($phone_number) && is_numeric($new)) {
			$newPhoneNumber = substr_replace($phone_number,"(",0).substr_replace($phone_number,")",3)." ".substr_replace(substr($phone_number,3),"-",3).substr($phone_number,6);
			$phoneNumber->number = $newPhoneNumber;
			$phoneNumber->save();
		}
		}
//		if($phone_number[3] == "-")// || $phone_number[3] == "-" || $phone_number[7] == "-"))
//		 {
//		$new = preg_replace("/[^A-Za-z0-9]/", "", $phone_number);
//		print_r($new);die();
//		//print_r($phone_number);die();
//		$newPhoneNumber = substr_replace($phone_number,"(",0).substr_replace($phone_number,")",3)." ".substr_replace($phone_number,"-",4).substr($phone_number,6);
////	$phoneNumber->number = $newPhoneNumber;
////	$phoneNumber->save();
//		}
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
