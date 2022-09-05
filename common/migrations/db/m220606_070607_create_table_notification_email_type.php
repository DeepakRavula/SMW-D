<?php

use yii\db\Migration;
use common\models\User;
use common\models\NotificationEmailType;

/**
 * Class m220606_070607_create_table_notification_email_type
 */
class m220606_070607_create_table_notification_email_type extends Migration
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
        $tableSchema = Yii::$app->db->schema->getTableSchema('notification_email_type');

        if ($tableSchema == null){
        $this->createTable('notification_email_type', [
            'id' => $this->primaryKey(),
            'emailNotifyType' => $this->string(255)->notNull(),
                'createdOn' => $this->timeStamp()->defaultValue(null),
                'updatedOn' => $this->timeStamp()->defaultValue(null),
		        'createdByUserId' =>  $this->integer()->notNull(),
                'updatedByUserId' =>  $this->integer()->notNull(),
                'isDeleted' => $this->boolean()->notNull(),
        ]);
    }
    $notifyTypes = ['Upcoming Makeup lesson', 'First scheduled Lesson', 'OverDue Invoice', 'Future Lessons',];
            foreach ($notifyTypes as $type) {
                $notifyReason = new NotificationEmailType();
                $notifyReason->emailNotifyType = $type;
                $notifyReason->isDeleted = 0;
                $notifyReason->save();
            }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220606_070607_create_table_notification_email_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220606_070607_create_table_notification_email_type cannot be reverted.\n";

        return false;
    }
    */
}