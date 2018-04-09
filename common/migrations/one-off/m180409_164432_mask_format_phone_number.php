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
	    //$slength = strlen($phoneNumber);
	    //print_r($phoneNumbers);die();
//		    $num = substr();
//		    $phone_number = $phoneNumbers->number;
//		    $num = substr($phone_number,0);
//		    print_r($num);die();
	foreach($phoneNumbers as $phoneNumber)
	{
		$phone_number = $phoneNumber->number;
		$phone_numbers = new UserPhone();
		if(is_numeric($phone_number)) {
		$second = substr_replace($phone_number,"(",0).$phone_number;
		$third = substr_replace($second,")",4)." ".substr($phone_number,3);
		$fourth = substr_replace($third,"-",9).substr($third,9);
		print_r($fourth);
	}
	//->save();
	 $phoneNumber->updateAttributes([
            ]);
	//$phone_number->save();
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
