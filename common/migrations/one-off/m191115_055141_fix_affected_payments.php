<?php

use yii\db\Migration;
use common\models\Location;
use common\models\User;
use common\models\Payment;
use yii\Helpers\Console;

/**
 * Class m191115_055141_fix_affected_payments
 */
class m191115_055141_fix_affected_payments extends Migration
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
        $locationIds = [1, 4, 9, 14, 15, 16, 17, 18, 19, 20, 21];
        Console::startProgress(0, 100, 'Affected Payments');
        $lessonCount = 0;
        $locations = Location::find()->andWhere(['id' => $locationIds])->all();
        foreach ($locations as $location) {
            $payments = Payment::find()
            ->notDeleted()
            ->exceptAutoPayments()
            ->all();
            foreach ($payments as $payment) {
                $oldBalance = $payment->balance;
                $payment->save(false);
                $newBalance = $payment->balance;
                if (round($oldBalance ,2)!= round($newBalance, 2)) {
                    if ($payment->user) {
                        print_r("\n".$payment->id."Customer: ".$payment->user->publicIdentity."(".$payment->user->id.")"."Old Balance:".round($oldBalance, 2)."New Balance:".round($newBalance, 2));
                    } else{
                        print_r("\n".$payment->id);
                    }
                }
                   
            }
            // $users = User::find()
            // ->customers($location->id)
            // ->notDeleted()
            // ->all();
            // foreach ($users as $user) {
            //         $balance = round(($user->getLessonsDue($user->id) + $user->getInvoiceOwingAmountTotal($user->id) - $user->getTotalCredits($user->id)), 2);
            //         if (round($balance, 2) !== round($user->customerAccount->balance, 2)) {
            //             print_r("\n Customer:".$user->publicIdentity."(".$user->id.")\t Location: ".$user->userLocation->location->name."\tCalculated Balance:".$balance."\tcustomer account balance:".$user->customerAccount->balance);
            //         }
                   
            // }

        
        }
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191115_055141_fix_affected_payments cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191115_055141_fix_affected_payments cannot be reverted.\n";

        return false;
    }
    */
}
