<?php

use yii\db\Migration;
use common\models\User;
use common\models\Location;

/**
 * Class m180814_093431_location_walkin_customer
 */
class m180814_093431_location_walkin_customer extends Migration
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
        $tableSchema = Yii::$app->db->schema->getTableSchema('location_walkin_customer');
        if ($tableSchema == null) {
            $this->createTable('location_walkin_customer', [
                'id' => $this->primaryKey(),
                'locationId' => $this->integer()->notNull(),
                'customerId' => $this->integer()->notNull()
            ]);
        }
        $locations = Location::find()->all();
        foreach ($locations as $location) {
            $location->addWalkinCustomer();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180814_093431_location_walkin_customer cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180814_093431_location_walkin_customer cannot be reverted.\n";

        return false;
    }
    */
}
