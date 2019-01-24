<?php

use yii\db\Migration;
use common\models\Payment;
use common\models\User;
use common\models\Lesson;
use common\models\LessonHierarchy;
use common\models\LessonPayment;

/**
 * Class m190124_044833_fix_hadi_dayoub_account
 */
class m190124_044833_fix_hadi_dayoub_account extends Migration
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
        $lessons = Lesson::find()->andWhere(['>','id', 207450])->andWhere(['courseId' => 687])->all();
        foreach ($lessons as $lesson) {
            if(!$lesson->leafs) {
          if($lesson->hasRootLesson()) {  
          $immediateRootLesson = $lesson->immediateRootLesson; 
          if($immediateRootLesson->status === Lesson::STATUS_CANCELED){   
          $immediateRootLesson->status = Lesson::STATUS_SCHEDULED;
          $immediateRootLesson->save();
          }
          if($lesson->hasPayment()) {
             $lessonPayments = $lesson->lessonPayments;
             foreach($lessonPayments as $lessonPayment){
                $lessonPayment->updateAttributes(['lessonId' => $immediateRootLesson->id]);
                if ($lessonPayment->payment->creditUsage) {
                    $lessonPayment->payment->creditUsage->debitUsagePayment->updateAttributes(['reference' => $immediateRootLesson->lessonNumber]);
                }
             }
          }
          $lesson->delete();
          $this->execute("DELETE FROM lesson_hierarchy where lesson_hierarchy.lessonId = ".$immediateRootLesson->id." AND lesson_hierarchy.childLessonId =".$lesson->id);
          $this->execute("DELETE FROM lesson_hierarchy where lesson_hierarchy.lessonId = ".$lesson->id." AND lesson_hierarchy.childLessonId =".$lesson->id);            
          
        }     
    }
    }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190124_044833_fix_hadi_dayoub_account cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190124_044833_fix_hadi_dayoub_account cannot be reverted.\n";

        return false;
    }
    */
}
