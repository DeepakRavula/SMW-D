<?php

use yii\db\Migration;
use common\models\log\Log;

/**
 * Class m180529_184148_fix_student_merge_log
 */
class m180529_184148_fix_student_merge_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $messages=Log::find()
        ->select(['message', "count('*') as messageCount"])
        ->groupBy('message')
        ->having([ '>','messageCount', 1])
        ->all();
        foreach($messages as $message){
            $logs=Log::find()->andWhere(['message'=> $message->message])->all();
            foreach($logs as $i => $log){
                if($i!=0)
                {
                   $log->delete();
                }
            }
            
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180529_184148_fix_student_merge_log cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180529_184148_fix_student_merge_log cannot be reverted.\n";

        return false;
    }
    */
}
