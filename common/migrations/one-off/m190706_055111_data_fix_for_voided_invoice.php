<?php

use yii\db\Migration;
use common\models\Invoice;
use common\models\User;
use common\models\Location;
use yii\helpers\Console;

/**
 * Class m190706_055111_data_fix_for_voided_invoice
 */
class m190706_055111_data_fix_for_voided_invoice extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $user = User::findByRole(User::ROLE_BOT);
        $botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }

    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $locationIds = [1, 4, 9, 13, 14, 15, 16, 17, 18, 19, 20, 21];
        $invoiceIds = [1807,5612];
        Console::startProgress(0, 100, 'Making void invoices to zero!!!');
        $locations = Location::find()->andWhere(['id' => $locationIds])->all();
        foreach ($locations as $location) {
            Console::output("Processing Location  " . $location->name, Console::FG_GREEN, Console::BOLD);
        $invoices = Invoice::find()
                ->notDeleted()
                ->andWhere(['isVoid' => 1])
                ->andWhere(['NOT', ['invoice.user_id' => $invoiceIds]])
                ->location($location->id)
                ->all();
        foreach ($invoices as $invoice) {
            Console::output("Affected invoice: " . $invoice->id, Console::FG_GREEN, Console::BOLD);
            $invoice->tax = 0.00;
            $invoice->save();
        }
    }
    Console::endProgress(true);
    Console::output("done.", Console::FG_GREEN, Console::BOLD);
}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190706_055111_data_fix_for_voided_invoice cannot be reverted.\n";

        return false;
    }
}
