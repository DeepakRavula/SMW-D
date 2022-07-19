<?php

use yii\db\Migration;

/**
 * Class m220718_121116_customer_email_notification
 */
class m220718_121116_customer_email_notification extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('customer_email_notification');
        if ($tableSchema == null) {
            $this->createTable('customer_email_notification', [
                'id' => $this->primaryKey(),
                'userId' => $this->integer()->notNull(),
                'emailNotificationTypeId' => $this->integer()->notNull(),
                'isChecked' => $this->boolean()->notNull()
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220718_121116_customer_email_notification cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220718_121116_customer_email_notification cannot be reverted.\n";

        return false;
    }
    */
}
