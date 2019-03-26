<?php

use yii\db\Migration;
use common\models\User;
use common\models\Location;
use common\models\CustomerAccount;
/**
 * Class m190326_070702_add_customer_account_table
 */
class m190326_070702_add_customer_account_table extends Migration
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
        $tableSchema = Yii::$app->db->schema->getTableSchema('customer_account');

        if ($tableSchema == null) {
            $this->createTable('customer_account', [
                'id' => $this->primaryKey(),
                'customerId' => $this->integer()->notNull(),
                'balance' => $this->decimal(10, 4)->defaultValue(null),
                'createdOn' => $this->timeStamp()->defaultValue(null),
                'updatedOn' => $this->timeStamp()->defaultValue(null),
		        'createdByUserId' =>  $this->integer()->notNull(),
                'updatedByUserId' =>  $this->integer()->notNull(),
            ]);
        }
        // $locations = Location::find()
        //         ->notDeleted()
        //         ->cronEnabledLocations()
        //         ->all();
        // foreach ($locations as $location) {
            $customers = User::find()
                ->notDeleted()
                ->allCustomers()
                ->all();
            foreach ($customers as $customer) {
                $customerAccount = new CustomerAccount();
                $customerAccount->customerId = $customer->id;
                $customerAccount->balance = ($customer->getLessonsDue($customer->id) + $customer->getInvoiceOwingAmountTotal($customer->id)) - $customer->getTotalCredits($customer->id);
                if ($customerAccount->save()) {
                    print_r($customerAccount->id);
                } else {
                    print_r($customerAccount->getErrors());
                }
            }
        //}  
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190326_070702_add_customer_account_table cannot be reverted.\n";

        return false;
    }
}
