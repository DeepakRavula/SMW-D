<?php

use yii\db\Migration;
use common\models\TextTemplate;

class m170802_090858_add_default_text extends Migration
{
    public function up()
    {
        $textTemplate = new TextTemplate();
        $textTemplate->message = 'Please find the invoice below';
        $textTemplate->type = TextTemplate::TYPE_INVOICE;
        $textTemplate->save();

        $textTemplate->isNewRecord = true;
        $textTemplate->id = null;
        $textTemplate->message = 'Please find the proforma invoice below';
        $textTemplate->type = TextTemplate::TYPE_PFI;
        $textTemplate->save();
    }

    public function down()
    {
        echo "m170802_090858_add_default_text cannot be reverted.\n";

        return false;
    }
}
