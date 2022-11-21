<?php

use yii\db\Migration;

/**
 * Handles adding is_online to table `enrolment`.
 */
class m210112_054325_add_is_online_column_to_enrolment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $table = Yii::$app->db->schema->getTableSchema('enrolment');
        // if(!isset($table->columns['is_online'])) {
            // Column doesn't exist
            $this->addColumn('enrolment', 'is_online', $this->tinyInteger()->defaultValue(0)->after('isAutoRenew'));
        //}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210112_054325_add_is_online_column_to_enrolment_table cannot be reverted.\n";

        return false;
    }
}
