<?php

use yii\db\Migration;

/**
 * Class m220728_074712_set_auto_email_status
 */
class m220728_074712_set_auto_email_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            'lesson',
            'auto_email_status',
            $this->boolean()->notNull()->after('status')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220728_074712_set_auto_email_status cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220728_074712_set_auto_email_status cannot be reverted.\n";

        return false;
    }
    */
}
