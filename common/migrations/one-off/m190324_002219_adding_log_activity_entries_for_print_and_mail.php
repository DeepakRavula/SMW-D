<?php

use yii\db\Migration;
use common\models\log\LogActivity;
/**
 * Class m190324_002219_adding_log_activity_entries_for_print_and_mail
 */
class m190324_002219_adding_log_activity_entries_for_print_and_mail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $logActivityNames = ['print', 'mail'];
        foreach ($logActivityNames as $logActivityName) {
            $logActivity       = new LogActivity();
            $logActivity->name = $logActivityName;
            $logActivity->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190324_002219_adding_log_activity_entries_for_print_and_mail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190324_002219_adding_log_activity_entries_for_print_and_mail cannot be reverted.\n";

        return false;
    }
    */
}
