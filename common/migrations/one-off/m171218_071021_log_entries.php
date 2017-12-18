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
            $log = new Log();
            $relationName = $this->getRelationName($timeLineEvent);
            if (!empty($relationName)) {
                $logActivity = LogActivity::findOne(['name' => $relationName['name']->action]);
                $log->logActivityId = $logActivity->id;
                $logObject = LogObject::findOne(['name' => $relationName['message']]);
                $log->logObjectId = $logObject->id;
                $log->data = yii\helpers\Json::encode($timeLineEvent['data']);
                $log->message = $timeLineEvent->message;
                $log->locationId = $timeLineEvent->locationId;
                $log->createdOn = $timeLineEvent->created_at;
                $log->createdUserId = $timeLineEvent->createdUserId;
                if (!($log->save())) {
                    print_r($log->getErrors());
                } else {
                    if ($timeLineEvent->links) {
                        $timeLineEventLink = $timeLineEvent->links;
                        $logLink = new LogLink();
                        $logLink->logId = $log->id;
                        $logLink->index = $timeLineEventLink->index;
                        $logLink->baseUrl = $timeLineEventLink->baseUrl;
                        $logLink->path = $timeLineEventLink->path;
                        $logLink->save();
                    }
                    $logHistory = new LogHistory();
                    $logHistory->logId = $log->id;
                    $logHistory->instanceType = $logObject->name;
                    $instanceName = $relationName['message'] . 'Id';
                    $logHistory->instanceId = $relationName['name']->$instanceName;
                    $logHistory->save();
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
        $relationName['name'] = '';
        $relationName['message'] = '';
        if ($timeLineEvent->timelineEventCourse) {
            $relationName['name'] = $timeLineEvent->timelineEventCourse;
            $relationName['message'] = 'course';
        }
        if ($timeLineEvent->timelineEventEnrolment) {
            $relationName['name'] = $timeLineEvent->timelineEventEnrolment;
            $relationName['message'] = 'enrolment';
        }
        if ($timeLineEvent->timelineEventInvoice) {
            $relationName['name'] = $timeLineEvent->timelineEventInvoice;
            $relationName['message'] = 'invoice';
        }
        if ($timeLineEvent->timelineEventLesson) {
            $relationName['name'] = $timeLineEvent->timelineEventLesson;
            $relationName['message'] = 'lesson';
        }
        if ($timeLineEvent->timelineEventPayment) {
            $relationName['name'] = $timeLineEvent->timelineEventPayment;
            $relationName['message'] = 'payment';
        }
        if ($timeLineEvent->timelineEventStudent) {
            $relationName['name'] = $timeLineEvent->timelineEventStudent;
            $relationName['message'] = 'student';
        }
        if ($timeLineEvent->timelineEventUser) {
            $relationName['name'] = $timeLineEvent->timelineEventUser;
            $relationName['message'] = 'user';
        }
        return $relationName;
    }
}
