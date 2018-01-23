<?php

use yii\db\Migration;
use common\models\Vacation;

class m170809_100542_vacation_deletion extends Migration
{
    public function up()
    {
        $vacations = Vacation::find()->all();
        foreach ($vacations as $vacation) {
            $vacation->updateAttributes([
                'isDeleted' => false
            ]);
        }
    }

    public function down()
    {
        echo "m170809_100542_vacation_deletion cannot be reverted.\n";

        return false;
    }
}
