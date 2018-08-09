<?php

use yii\db\Migration;

/**
 * Class m180808_060947_add_soft_delete
 */
class m180808_060947_add_soft_delete extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('blog', 'isDeleted', $this->boolean()->notNull());
        $this->addColumn('city', 'isDeleted', $this->boolean()->notNull());
        $this->addColumn('customer_payment_preference', 'isDeleted', $this->boolean()->notNull());
        $this->addColumn('exam_result', 'isDeleted', $this->boolean()->notNull());
        $this->addColumn('holiday', 'isDeleted', $this->boolean()->notNull());        
        $this->addColumn('location', 'isDeleted', $this->boolean()->notNull());        
        $this->addColumn('location_availability', 'isDeleted', $this->boolean()->notNull());                       
        $this->addColumn('program', 'isDeleted', $this->boolean()->notNull());        
        $this->addColumn('teacher_availability_day', 'isDeleted', $this->boolean()->notNull());        
        $this->addColumn('user_address', 'isDeleted', $this->boolean()->notNull());        
        $this->addColumn('user_contact', 'isDeleted', $this->boolean()->notNull());        
        $this->addColumn('user_email', 'isDeleted', $this->boolean()->notNull());        
        $this->addColumn('user_phone', 'isDeleted', $this->boolean()->notNull());        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180808_060947_add_soft_delete cannot be reverted.\n";

        return false;
    }
}
