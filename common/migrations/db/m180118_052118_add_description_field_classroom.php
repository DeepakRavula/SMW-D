<?php

use yii\db\Migration;

class m180118_052118_add_description_field_classroom extends Migration
{
    public function up()
    {
        $this->addColumn('classroom', 'description', 'TEXT NOT NULL AFTER name');
    }

    public function down()
    {
        echo "m180118_052118_add_description_field_classroom cannot be reverted.\n";

        return false;
    }
}
