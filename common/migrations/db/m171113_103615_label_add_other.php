<?php

use yii\db\Migration;
use common\models\Label;

class m171113_103615_label_add_other extends Migration
{
    public function up()
    {
        $label = Label::findOne(['id' =>Label::LABEL_OTHER]);
        $label->updateAttributes([
            'name' => 'Other'
        ]);
    }

    public function down()
    {
        echo "m171113_103615_label_add_other cannot be reverted.\n";

        return false;
    }
}
