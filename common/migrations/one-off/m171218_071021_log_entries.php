<?php

use yii\db\Migration;
use common\models\log\Log;
use common\models\log\LogActivity;
use common\models\log\LogHistory;
use common\models\log\LogLink;
use common\models\log\LogObject;
use common\models\timelineEvent\TimelineEvent;

class m171218_071021_log_entries extends Migration
{
    public function up()
    {
        $timeLineEvents = TimelineEvent::find()->all();
        foreach ($timeLineEvents as $timeLineEvent) {
            $log          = new Log();
            $relationName = $this->getRelationName($timeLineEvent);
            if (!empty($relationName)) {
                $logActivity        = LogActivity::findOne(['name' => $relationName['object']->action]);
                $log->logActivityId = $logActivity->id;
                $logObject          = LogObject::findOne(['name' => $relationName['instanceType']]);
                $log->logObjectId   = $logObject->id;
                $log->data          = json_encode($timeLineEvent->data);
                $log->message       = $timeLineEvent->message;
                $log->locationId    = $timeLineEvent->locationId;
                $log->createdOn     = $timeLineEvent->created_at;
                $log->createdUserId = $timeLineEvent->createdUserId;
                try {
                    $log->save();
                } catch (ErrorException $exception) {
                    Yii::$app->errorHandler->logException($exception);
                }
                $logHistory               = new LogHistory();
                $logHistory->logId        = $log->id;
                $logHistory->instanceType = $logObject->name;
                $instanceName             = $relationName['instanceType'].'Id';
                $logHistory->instanceId   = $relationName['object']->$instanceName;
                $logHistory->save();
                if (!empty($timeLineEvent->links)) {
                    $timeLineEventLinks = $timeLineEvent->links;

                    foreach ($timeLineEventLinks as $timeLineEventLink) {
                        $logLink          = new LogLink();
                        $logLink->logId   = $log->id;
                        $logLink->index   = $timeLineEventLink->index;
                        $logLink->baseUrl = $timeLineEventLink->baseUrl;
                        $logLink->path    = $timeLineEventLink->path;
                        $logLink->save();
                    }
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
        $relationName['object']       = '';
        $relationName['instanceType'] = '';
        if ($timeLineEvent->timelineEventCourse) {
            $relationName['object']       = $timeLineEvent->timelineEventCourse;
            $relationName['instanceType'] = 'course';
        }
        if ($timeLineEvent->timelineEventEnrolment) {
            $relationName['object']       = $timeLineEvent->timelineEventEnrolment;
            $relationName['instanceType'] = 'enrolment';
        }
        if ($timeLineEvent->timelineEventInvoice) {
            $relationName['object']       = $timeLineEvent->timelineEventInvoice;
            $relationName['instanceType'] = 'invoice';
        }
        if ($timeLineEvent->timelineEventLesson) {
            $relationName['object']       = $timeLineEvent->timelineEventLesson;
            $relationName['instanceType'] = 'lesson';
        }
        if ($timeLineEvent->timelineEventPayment) {
            $relationName['object']       = $timeLineEvent->timelineEventPayment;
            $relationName['instanceType'] = 'payment';
        }
        if ($timeLineEvent->timelineEventStudent) {
            $relationName['object']       = $timeLineEvent->timelineEventStudent;
            $relationName['instanceType'] = 'student';
        }
        if ($timeLineEvent->timelineEventUser) {
            $relationName['object']       = $timeLineEvent->timelineEventUser;
            $relationName['instanceType'] = 'user';
        }
        return $relationName;
    }
}
