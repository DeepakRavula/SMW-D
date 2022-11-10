<?php

use yii\db\Migration;

class m170714_084351_change_mark_data_type extends Migration
{
    public function up()
    {
        $this->alterColumn('exam_result', 'mark', $this->decimal(10, 2));
    }

    public function down()
    {
        echo "m170714_084351_change_mark_data_type cannot be reverted.\n";

        return false;
    }
}
