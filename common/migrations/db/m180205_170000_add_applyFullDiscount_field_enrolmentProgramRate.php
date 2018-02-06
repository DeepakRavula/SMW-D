<?php

use yii\db\Migration;

/**
 * Class m180205_170000_add_applyFullDiscount_field_enrolmentProgramRate
 */
class m180205_170000_add_applyFullDiscount_field_enrolmentProgramRate extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('enrolment_program_rate', 'applyFullDiscount', $this->boolean()->notNull()->after('programRate'));
        $this->alterColumn('course', 'endDate', $this->timestamp()->null());
        $this->addColumn('course', 'type', $this->integer()->notNull()->defaultValue(1)->after('locationId'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180205_170000_add_applyFullDiscount_field_enrolmentProgramRate cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180205_170000_add_applyFullDiscount_field_enrolmentProgramRate cannot be reverted.\n";

        return false;
    }
    */
}
