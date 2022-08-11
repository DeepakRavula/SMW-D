<?php

use yii\db\Migration;
use common\models\PrivateLessonEmailStatus;

/**
 * Class m220811_101220_delete_email_records
 */
class m220811_101220_delete_email_records extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        PrivateLessonEmailStatus::deleteAll();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220811_101220_delete_email_records cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220811_101220_delete_email_records cannot be reverted.\n";

        return false;
    }
    */
}
