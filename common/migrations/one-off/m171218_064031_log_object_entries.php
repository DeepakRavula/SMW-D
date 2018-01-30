<?php

use yii\db\Migration;
use common\models\log\LogObject;

class m171218_064031_log_object_entries extends Migration
{
    public function up()
    {
        $logObjectNames = ['course', 'enrolment', 'lesson', 'invoice', 'payment',
            'student', 'user'];
        foreach ($logObjectNames as $logObjectName) {
            $logObject       = new LogObject();
            $logObject->name = $logObjectName;
            $logObject->save();
        }
    }

    public function down()
    {
        echo "m171218_064031_log_object_entries cannot be reverted.\n";

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
