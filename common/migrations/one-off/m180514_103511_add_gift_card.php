<?php

use yii\db\Migration;

/**
 * Class m180514_103511_add_gift_card
 */
class m180514_103511_add_gift_card extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('payment_method',array(
            'name'=>'Gift Card',
            'active' => 1,
            'displayed' => 1,
            'sortOrder' => 8
     ));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180514_103511_add_gift_card cannot be reverted.\n";

        return false;
    }
}
