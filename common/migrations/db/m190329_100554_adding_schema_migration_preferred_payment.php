<?php

use yii\db\Migration;

/**
 * Class m190329_100554_adding_schema_migration_preferred_payment
 */
class m190329_100554_adding_schema_migration_preferred_payment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('customer_recurring_payment');
        if ($tableSchema == null) {
            $this->createTable('customer_recurring_payment', [
                'id' => $this->primaryKey(),
                'customerId' => $this->integer()->notNull(),
                'entryDay' => $this->date()->notNull(),
                'paymentDay' => $this->date()->notNull(),
                'paymentMethodId' => $this->integer()->notNull(),
                'paymentFrequencyId' => $this->integer()->notNull(),
                'expiryDate' => $this->date()->notNull(),
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
        echo "m190329_100554_adding_schema_migration_preferred_payment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190329_100554_adding_schema_migration_preferred_payment cannot be reverted.\n";

        return false;
    }
    */
}
