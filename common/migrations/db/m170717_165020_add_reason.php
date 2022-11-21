<?php

use yii\db\Migration;

class m170717_165020_add_reason extends Migration
{
    public function up()
    {
        $this->addColumn(
            'teacher_unavailability',
            'reason',
            $this->text()->after('toTime')
        );
    }

    public function down()
    {
        echo "m170717_165020_add_reason cannot be reverted.\n";

        return false;
    }
}
