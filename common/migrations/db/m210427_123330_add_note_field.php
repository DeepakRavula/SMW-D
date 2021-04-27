<?php

use yii\db\Migration;

/**
 * Class m210427_123330_add_note_field
 */
class m210427_123330_add_note_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user_email', 'note', $this->text()->after('email'));
        $this->addColumn('user_phone', 'note', $this->text()->after('extension'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210427_123330_add_note_field cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210427_123330_add_note_field cannot be reverted.\n";

        return false;
    }
    */
}
