<?php

use yii\db\Migration;

/**
 * Class m190311_071330_payment_balance
 */
class m190311_071330_payment_balance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = Yii::$app->db->schema->getTableSchema('payment');
        if (!isset($table->columns['balance'])) {
            $this->addColumn('payment', 'balance', $this->decimal(10,4)->notNull()->defaultValue(0));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190311_071330_payment_balance cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190311_071330_payment_balance cannot be reverted.\n";

        return false;
    }
    */
}
