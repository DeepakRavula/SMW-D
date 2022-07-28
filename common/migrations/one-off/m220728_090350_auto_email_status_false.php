<?php

use yii\db\Migration;
use common\models\Lesson;

/**
 * Class m220728_090350_auto_email_status_false
 */
class m220728_090350_auto_email_status_false extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $autoEmailStatus = Lesson::find()->all();

        foreach ($autoEmailStatus as $update) {
        
            $update->updateAttributes(['auto_email_status' => false]);
        
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220728_090350_auto_email_status_false cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220728_090350_auto_email_status_false cannot be reverted.\n";

        return false;
    }
    */
}
