<?php

use yii\db\Migration;
use common\models\log\Log;
use common\models\log\LogActivity;
use common\models\log\LogHistory;
use common\models\log\LogLink;
use common\models\log\LogObject;
use common\models\timelineEvent\TimelineEvent;
use common\models\timelineEvent\TimelineEventCourse;
use common\models\timelineEvent\TimelineEventLink;

class m171218_071021_log_entries extends Migration
{
    public function up()
    {
        $timeLineEvents= TimelineEvent::find()->all();
        foreach($timeLineEvents as $timeLineEvent)
        {
          $log=new Log();
          $relationName = $this->getRelationName($timeLineEvent);
          if(!empty($relationName))
          {
              $logActivity= LogActivity::findOne(['name' => $relationName['name']->action]);
              $log->logActivityId=$logActivity->id;
              $logObject=LogObject::findOne(['name' => $relationName['message']]);
              $log->logObjectId=$logObject->id;
          $log->data=yii\helpers\Json::encode($timeLineEvent['data']);
          $log->message=$timeLineEvent->message;
          $log->locationId=$timeLineEvent->locationId;
          $log->createdOn=$timeLineEvent->created_at;
          $log->createdUserId=$timeLineEvent->createdUserId;
          if(!($log->save()))
          {
             print_r($log->getErrors());
          }

        }
        }
    }

    public function down()
    {
        echo "m171218_071021_log_entries cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
    public function getRelationName($timeLineEvent)
    {
        $relationName['name']='';
        $relationName['message']='';
        if($timeLineEvent->timelineEventCourse)
        {
            $relationName['name']=$timeLineEvent->timelineEventCourse;
            $relationName['message'] = 'Course';
        }
        if($timeLineEvent->timelineEventEnrolment)
        {
            $relationName['name']=$timeLineEvent->timelineEventEnrolment;
            $relationName['message'] = 'Enrolment';
        }
        if($timeLineEvent->timelineEventInvoice)
        {
            $relationName['name']=$timeLineEvent->timelineEventInvoice;
            $relationName['message'] = 'Invoice';
        }
        if($timeLineEvent->timelineEventLesson)
        {
             $relationName['name']=$timeLineEvent->timelineEventLesson;
             $relationName['message'] = 'Lesson';
        }
        if($timeLineEvent->timelineEventPayment)
        {
            $relationName['name']=$timeLineEvent->timelineEventPayment;
            $relationName['message'] = 'Payment';
        }
        if($timeLineEvent->timelineEventStudent)
        {
            $relationName['name']=$timeLineEvent->timelineEventStudent;
            $relationName['message'] = 'Student';
        }
         if($timeLineEvent->timelineEventUser)
        {
            $relationName['name']=$timeLineEvent->timelineEventUser;
            $relationName['message'] = 'User';
        }
        return $relationName;
    }
}
