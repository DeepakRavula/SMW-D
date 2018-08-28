<?php

use yii\db\Migration;
use common\models\ReferralSource;
use common\models\User;
/**
 * Class m180826_182345_add_referal_source
 */
class m180826_182345_add_referal_source extends Migration
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
        $tableSchema = Yii::$app->db->schema->getTableSchema('referral_sources');

        if ($tableSchema == null) {
            $this->createTable('referral_sources', [
                'id' => $this->primaryKey(),
                'name' => $this->string(255)->notNull(),
                'createdOn' => $this->timeStamp()->defaultValue(null),
                'updatedOn' => $this->timeStamp()->defaultValue(null),
		        'createdByUserId' =>  $this->integer()->notNull(),
                'updatedByUserId' =>  $this->integer()->notNull(),
                'isDeleted' => $this->boolean()->notNull(),
            ]);
        }
        $sourceNames = ['Drive By', 'Arcadia\'s Website', 'Newspaper Advertisment', 'Other',];
            foreach ($sourceNames as $sourceName) {
                $referralSource       = new ReferralSource();
                $referralSource->name = $sourceName;
                $referralSource->save();
            }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180826_182345_add_referal_source cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180826_182345_add_referal_source cannot be reverted.\n";

        return false;
    }
    */
}
