<?php

use yii\db\Migration;

/**
 * Class m181009_071041_remove_enrolment_discount_unsigned_attribute
 */
class m181009_071041_remove_enrolment_discount_unsigned_attribute extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('enrolment_discount', 'discount', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181009_071041_remove_enrolment_discount_unsigned_attribute cannot be reverted.\n";

        return false;
    }
}
