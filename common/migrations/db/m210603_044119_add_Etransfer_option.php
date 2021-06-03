<?php

use yii\db\Migration;

/**
 * Class m210603_044119_add_Etransfer_option
 */
class m210603_044119_add_Etransfer_option extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('payment_method',array(
            'name'=>'E-Transfer',
            'active' => 1,
            'displayed' => 1,
            'sortOrder' => 9
     ));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210603_044119_add_Etransfer_option cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210603_044119_add_Etransfer_option cannot be reverted.\n";

        return false;
    }
    */
}
