<?php

use yii\db\Migration;

class m170721_091542_alter_enrolment_discount extends Migration
{
    public function up()
    {
        $this->addColumn(
            'enrolment_discount',
            'discountType',
            $this->integer()->notNull()->after('discount')
        );
        $this->addColumn(
            'enrolment_discount',
            'type',
            $this->integer()->notNull()->after('discountType')
        );
    }

    public function down()
    {
        echo "m170721_091542_alter_enrolment_discount cannot be reverted.\n";

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
