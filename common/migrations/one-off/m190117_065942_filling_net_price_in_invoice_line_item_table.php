<?php

use yii\db\Migration;
use common\models\InvoiceLineItem;
use common\models\User;

/**
 * Class m190117_065942_filling_net_price_in_invoice_line_item_table
 */
class m190117_065942_filling_net_price_in_invoice_line_item_table extends Migration
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
       
       $lineItems = InvoiceLineItem::find()
                        ->all();
        foreach ($lineItems as $lineItem) {
            $lineItem->updateAttributes(['priceAfterDiscounts' => $lineItem->netPrice]);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190117_065942_filling_net_price_in_invoice_line_item_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190117_065942_filling_net_price_in_invoice_line_item_table cannot be reverted.\n";

        return false;
    }
    */
}
