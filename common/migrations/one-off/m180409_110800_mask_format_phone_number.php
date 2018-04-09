<?php

use yii\db\Migration;
use common\models\UserPhone;
/**
 * Class m180409_110800_mask_format_phone_number
 */
class m180409_110800_mask_format_phone_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $phoneNumbers = UserPhone::find()->all();
	    //$slength = strlen($phoneNumber);
	foreach($phoneNumbers as $phoneNumber)
	{
	$slength = strlen($phoneNumber->number);
	$first = substr($phoneNumber->number,1);
	if(is_numeric($first)) {
		print_r($first);
	}
      
            print_r($slength);die();
        
    }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180409_110800_mask_format_phone_number cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180409_110800_mask_format_phone_number cannot be reverted.\n";

        return false;
    }
    */
}
