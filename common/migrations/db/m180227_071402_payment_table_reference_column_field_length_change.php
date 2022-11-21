<?php

use yii\db\Migration;

/**
 * Class m180227_071402_payment_table_reference_column_field_length_change
 */
class m180227_071402_payment_table_reference_column_field_length_change extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
	     $this->alterColumn('payment', 'reference', $this->String(255));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180227_071402_payment_table_reference_column_field_length_change cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180227_071402_payment_table_reference_column_field_length_change cannot be reverted.\n";

        return false;
    }
    */
}
