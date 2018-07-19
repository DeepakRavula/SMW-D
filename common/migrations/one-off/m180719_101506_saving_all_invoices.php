<?php

use yii\db\Migration;
use common\models\Invoice;
use common\models\User;

/**
 * Class m180719_101506_saving_all_invoices
 */
class m180719_101506_saving_all_invoices extends Migration
{
    public function init() 
    {
        parent::init();
        $user = User::findByRole(User::ROLE_BOT);
        $botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $invoices = Invoice::find()
        ->notDeleted()
        ->all();

    foreach ($invoices as $invoice) {
        $invoice->save();
    }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180719_101506_saving_all_invoices cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180719_101506_saving_all_invoices cannot be reverted.\n";

        return false;
    }
    */
}
