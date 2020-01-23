<?php

use yii\db\Migration;
use common\models\LessonPayment;
use common\models\Lesson;
use common\models\User;

/**
 * Class m200122_151203_fix_customer_spano_payment_details
 */
class m200122_151203_fix_customer_spano_payment_details extends Migration
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
        $lesson = Lesson::findOne('1079762');
        $lessonPayment = LessonPayment::find()->andWhere(['id' => 363474])->one();
        $lessonPayment->updateAttributes(['lessonId' => $lesson->id]);
        $lesson->creditTransfer($lesson->invoice);

        $cancelledLessons = Lesson::find()
                            ->all();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200122_151203_fix_customer_spano_payment_details cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200122_151203_fix_customer_spano_payment_details cannot be reverted.\n";

        return false;
    }
    */
}
