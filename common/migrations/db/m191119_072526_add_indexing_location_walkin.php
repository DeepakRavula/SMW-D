<?php

use yii\db\Migration;

/**
 * Class m191119_072526_add_indexing_location_walkin
 */
class m191119_072526_add_indexing_location_walkin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('customerId', 'customer_account', 'customerId');
        $this->createIndex('locationId', 'location_walkin_customer', 'locationId');
        $this->createIndex('customerId', 'location_walkin_customer', 'customerId');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191119_072526_add_indexing_location_walkin cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191119_072526_add_indexing_location_walkin cannot be reverted.\n";

        return false;
    }
    */
}
