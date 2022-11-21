<?php

use yii\db\Migration;
use common\models\log\LogActivity;

class m171218_070105_log_activity_entries extends Migration
{
    public function up()
    {
        $logActivityNames = ['create', 'edit', 'update', 'delete'];
        foreach ($logActivityNames as $logActivityName) {
            $logActivity       = new LogActivity();
            $logActivity->name = $logActivityName;
            $logActivity->save();
        }
    }

    public function down()
    {
        echo "m171218_070105_log_activity_entries cannot be reverted.\n";

        return false;
    }
    /*
      // Use safeUp/safeDown to run migration code within a transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
