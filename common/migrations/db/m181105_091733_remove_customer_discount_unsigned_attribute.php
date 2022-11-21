<?php

use yii\db\Migration;

/**
 * Class m181105_091733_remove_customer_discount_unsigned_attribute
 */
class m181105_091733_remove_customer_discount_unsigned_attribute extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('customer_discount', 'value', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181105_091733_remove_customer_discount_unsigned_attribute cannot be reverted.\n";

        return false;
    }
}
