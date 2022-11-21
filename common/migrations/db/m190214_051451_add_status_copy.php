<?php

use yii\db\Migration;

/**
 * Class m190214_051451_add_status_copy
 */
class m190214_051451_add_status_copy extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = Yii::$app->db->schema->getTableSchema('lesson');
        if (!isset($table->columns['paidStatus'])) {
            $this->addColumn('lesson', 'paidStatus', $this->integer()->notNull()->defaultValue(0));
        }
        if (!isset($table->columns['total'])) {
            $this->addColumn('lesson', 'total', $this->decimal(10, 4)->notNull()->defaultValue(0.00));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190214_051451_add_status_copy cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190214_051451_add_status_copy cannot be reverted.\n";

        return false;
    }
    */
}
