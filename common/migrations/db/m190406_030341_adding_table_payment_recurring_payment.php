<?php

use yii\db\Migration;

/**
 * Class m190406_030341_adding_table_payment_recurring_payment
 */
class m190406_030341_adding_table_payment_recurring_payment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('recurring_payment');
        if ($tableSchema == null) {
            $this->createTable('recurring_payment', [
                'id' => $this->primaryKey(),
                'paymentId' => $this->integer()->notNull(),
                'createdOn' => $this->timeStamp()->defaultValue(null),
                'updatedOn' => $this->timeStamp()->defaultValue(null),
                'createdByUserId' =>  $this->integer()->notNull(),
                'updatedByUserId' =>  $this->integer()->notNull(),
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190406_030341_adding_table_payment_recurring_payment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190406_030341_adding_table_payment_recurring_payment cannot be reverted.\n";

        return false;
    }
    */
}
