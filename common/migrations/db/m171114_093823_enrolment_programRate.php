<?php

use yii\db\Migration;

class m171114_093823_enrolment_programRate extends Migration
{
    public function up()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('enrolment_program_rate');
        if ($tableSchema == null) {
            $this->createTable('enrolment_program_rate', [
                'id' => $this->primaryKey()
            ]);
            $this->addColumn('enrolment_program_rate', 'enrolmentId', $this->integer()->notNull()->after('id'));
            $this->addColumn('enrolment_program_rate', 'startDate', $this->date()->notNull()->after('enrolmentId'));
            $this->addColumn('enrolment_program_rate', 'endDate', $this->date()->notNull()->after('startDate'));
            $this->addColumn('enrolment_program_rate', 'programRate', $this->decimal(10, 2)->notNull()->after('endDate'));
        }
    }

    public function down()
    {
        echo "m171114_093823_enrolment_programRate cannot be reverted.\n";

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
