<?php

use yii\db\Migration;
use common\models\UserPhone;

/**
 * Class m190104_163405_mask_phone_numbers_format_for_import_users
 */
class m190104_163405_mask_phone_numbers_format_for_import_users extends Migration
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
        echo "m190104_163405_mask_phone_numbers_format_for_import_users cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190104_163405_mask_phone_numbers_format_for_import_users cannot be reverted.\n";

        return false;
    }
    */
}
