<?php

use yii\db\Migration;

/**
 * Class m200214_052228_change_tonia_role
 */
class m200214_052228_change_tonia_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM rbac_auth_assignment where user_id =2");
        $this->execute("INSERT INTO rbac_auth_assignment(item_name,user_id) VALUES('owner','2')");
        $this->execute("INSERT INTO user_location(user_id, location_id) VALUES(2, 9)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200214_052228_change_tonia_role cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200214_052228_change_tonia_role cannot be reverted.\n";

        return false;
    }
    */
}
