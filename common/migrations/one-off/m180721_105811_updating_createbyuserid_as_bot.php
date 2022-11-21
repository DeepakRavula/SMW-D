<?php

use yii\db\Migration;
use common\models\ProformaInvoice;
use common\models\User;

/**
 * Class m180721_105811_updating_createbyuserid_as_bot
 */
class m180721_105811_updating_createbyuserid_as_bot extends Migration
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
        $proformaInvoices = ProformaInvoice::find()
                    ->andWhere(['between', 'id', 172, 278])
                    ->all();
        $user = User::findByRole(User::ROLE_BOT);
        $botUser = end($user);            
        foreach ($proformaInvoices as $proformaInvoice) {
            $proformaInvoice->updateAttributes(['createdByUserId' => $botUser->id]);
        }            
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180721_105811_updating_createbyuserid_as_bot cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180721_105811_updating_createbyuserid_as_bot cannot be reverted.\n";

        return false;
    }
    */
}
