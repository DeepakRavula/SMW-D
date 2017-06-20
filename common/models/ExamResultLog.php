<?php

namespace common\models;

use Yii;
use common\models\ExamResult;
use common\models\Student;
use common\commands\AddToTimelineCommand;
use yii\helpers\Url;
use common\models\timelineEvent\TimelineEventLink;
use common\models\timelineEvent\TimelineEventStudent;

/**
 * This is the model class for table "student".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $birth_date
 * @property int $customer_id
 */
class ExamResultLog extends ExamResult
{	
	public function create($event) {
            
		$examResultModel = $event->sender;
		$examresult = ExamResult::find(['id' => $examResultModel->id])->asArray()->one();
		$studentModel=Student::findOne(['id' =>$examResultModel->studentId ]);
                $timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
			'data' => $examresult,
			'message' => $examResultModel->userName.' created new Exam Result for {{' .$studentModel->fullName . '}}',
		]));
		if($timelineEvent) {
			$timelineEventLink = new TimelineEventLink();
			$timelineEventLink->timelineEventId = $timelineEvent->id;
			$timelineEventLink->index = $studentModel->fullName;
			$timelineEventLink->baseUrl = Yii::$app->request->hostInfo;
			$timelineEventLink->path = Url::to(['/student/view', 'id' => $studentModel->id]);
			$timelineEventLink->save();

			$timelineEventStudent = new TimelineEventStudent();
			$timelineEventStudent->studentId = $studentModel->id;
			$timelineEventStudent->timelineEventId = $timelineEvent->id;
			$timelineEventStudent->action = 'create';
			$timelineEventStudent->save();
		}
	}
	
	public function edit($event) {
	$examResultModel = $event->sender;
		$examresult = ExamResult::find(['id' => $examResultModel->id])->asArray()->one();
		$studentModel=Student::findOne(['id' =>$examResultModel->studentId ]);
                $timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
			'data' => $examresult,
			'message' => $examResultModel->userName.' Edited {{' .$studentModel->fullName . '}}\'s Exam Result',
		]));
		if($timelineEvent) {
			$timelineEventLink = new TimelineEventLink();
			$timelineEventLink->timelineEventId = $timelineEvent->id;
			$timelineEventLink->index = $studentModel->fullName;
			$timelineEventLink->baseUrl = Yii::$app->request->hostInfo;
			$timelineEventLink->path = Url::to(['/student/view', 'id' => $studentModel->id]);
			$timelineEventLink->save();

			$timelineEventStudent = new TimelineEventStudent();
			$timelineEventStudent->studentId = $studentModel->id;
			$timelineEventStudent->timelineEventId = $timelineEvent->id;
			$timelineEventStudent->action = 'edit';
			$timelineEventStudent->save();
		}
	}
      
        
        public function deleteEvaluation($event) {
	$examResultModel = $event->sender;
		$examresult = ExamResult::find(['id' => $examResultModel->id])->asArray()->one();
		$studentModel=Student::findOne(['id' =>$examResultModel->studentId ]);
                $timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
			'data' => $examresult,
			'message' => $examResultModel->userName.' deleted {{' .$studentModel->fullName . '}}\'s Exam Result',
		]));
		if($timelineEvent) {
			$timelineEventLink = new TimelineEventLink();
			$timelineEventLink->timelineEventId = $timelineEvent->id;
			$timelineEventLink->index = $studentModel->fullName;
			$timelineEventLink->baseUrl = Yii::$app->request->hostInfo;
			$timelineEventLink->path = Url::to(['/student/view', 'id' => $studentModel->id]);
			$timelineEventLink->save();

			$timelineEventStudent = new TimelineEventStudent();
			$timelineEventStudent->studentId = $studentModel->id;
			$timelineEventStudent->timelineEventId = $timelineEvent->id;
			$timelineEventStudent->action = 'delete';
			$timelineEventStudent->save();
		}
	}
      
        
        
        
        
}
