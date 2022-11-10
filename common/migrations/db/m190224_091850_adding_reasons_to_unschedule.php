<?php

use yii\db\Migration;
use common\models\ReasonsToUnschedule;
use common\models\User;

/**
 * Class m190224_091850_adding_reasons_to_unschedule
 */
class m190224_091850_adding_reasons_to_unschedule extends Migration
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
        $tableSchema = Yii::$app->db->schema->getTableSchema('reasons_to_unschedule');

        if ($tableSchema == null) {
            $this->createTable('reasons_to_unschedule', [
                'id' => $this->primaryKey(),
                'reason' => $this->string(255)->notNull(),
                'createdOn' => $this->timeStamp()->defaultValue(null),
                'updatedOn' => $this->timeStamp()->defaultValue(null),
		        'createdByUserId' =>  $this->integer()->notNull(),
                'updatedByUserId' =>  $this->integer()->notNull(),
                'isDeleted' => $this->boolean()->notNull(),
            ]);
        }
        $reasons = ['the student cancelled', 'the teacher cancelled', 'the school closed due to the weather', 'Other',];
            foreach ($reasons as $reason) {
                $reasonToUnschedule      = new ReasonsToUnschedule();
                $reasonToUnschedule->reason = $reason;
                $reasonToUnschedule->save();
            }


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190224_091850_adding_reasons_to_unschedule cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190224_091850_adding_reasons_to_unschedule cannot be reverted.\n";

        return false;
    }
    */
}
