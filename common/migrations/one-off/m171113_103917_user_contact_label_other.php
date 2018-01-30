<?php

use yii\db\Migration;
use common\models\UserContact;
use common\models\Label;

class m171113_103917_user_contact_label_other extends Migration
{
    public function up()
    {
        $userContacts = UserContact::find()
            ->andWhere(['labelId' => Label::LABEL_OTHER])
            ->all();
        foreach ($userContacts as $userContact) {
            $userContact->updateAttributes(['labelId' => Label::LABEL_WORK]);
        }
    }

    public function down()
    {
        echo "m171113_103917_user_contact_label_other cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
