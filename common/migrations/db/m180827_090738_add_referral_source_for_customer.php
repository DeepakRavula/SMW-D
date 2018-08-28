<?php

use yii\db\Migration;

/**
 * Class m180827_090738_add_referral_source_for_customer
 */
class m180827_090738_add_referral_source_for_customer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('customer_referral_sources');
        
        if ($tableSchema == null) {
            $this->createTable('customer_referral_sources', [
                'id' => $this->primaryKey(),
                'userId' => $this->integer(),
                'referralSourceId' => $this->integer(),
                'description' => $this->string(255),
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
        echo "m180827_090738_add_referral_source_for_customer cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180827_090738_add_referral_source_for_customer cannot be reverted.\n";

        return false;
    }
    */
}
