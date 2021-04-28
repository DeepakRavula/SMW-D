<?php

namespace backend\controllers;

use Yii;
use common\models\Location;
use common\models\Lesson;
use common\models\Enrolment;
use common\models\Course;
use yii\filters\AccessControl;
use common\models\Student;
use common\components\controllers\BaseController;
use backend\models\search\DashboardSearch;
use common\models\User;
use Carbon\Carbon;

class DashboardController extends BaseController
{
	
public function behaviors()
{
    return [
        'access' => [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index'],
                    'roles' => ['loginToBackend'],
                ],
            ],
        ],
    ];
}
    public function actionIndex()
    {
        if (!$this->canView()) {
            $this->redirect(['schedule/index']);
        }
        $searchModel = new DashboardSearch();
        $currentDate = new \DateTime();
        $searchModel->fromDate = $currentDate->format('M 1, Y');
        $searchModel->toDate = $currentDate->format('M t, Y');
        $searchModel->dateRange = $searchModel->fromDate.' - '.$searchModel->toDate;
        $request = Yii::$app->request;
        if ($searchModel->load($request->get())) {
            $dashboardRequest = $request->get('DashboardSearch');
            $searchModel->dateRange = $dashboardRequest['dateRange'];
	        list($searchModel->fromDate, $searchModel->toDate) = explode(' - ', $searchModel->dateRange);
        }
        $toDate = $searchModel->toDate;
        if ($toDate > $currentDate) {
            $toDate = $currentDate;
        }
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
	    $fromDate = Carbon::parse($searchModel->fromDate);	 
        $from = $fromDate->format('Y-m-d');
	    $toDate = Carbon::parse($searchModel->toDate);
        $to = $toDate->format('Y-m-d');
        $enrolments = Enrolment::find()
            ->notDeleted()
            ->joinWith(['course' => function ($query) use ($locationId, $searchModel,$from,$to) {
                $query->joinWith(['program' => function ($query) {
                    $query->privateProgram();
                }])
                ->confirmed()
                ->notDeleted()
	            ->overlap($from, $to)
                ->andWhere(['course.type' => Course::TYPE_REGULAR])
                ->location($locationId);
            }])
            ->count('studentId');

	    
        $groupEnrolments = Enrolment::find()
            ->joinWith(['course' => function ($query) use ($locationId, $searchModel,$from,$to) {
                $query->joinWith(['program' => function ($query) {
                    $query->group();
                }])
                ->confirmed()
                ->notDeleted()
		        ->overlap($from, $to)
                ->andWhere(['course.type' => Course::TYPE_REGULAR])
                ->location($locationId);
            }])
            ->count('studentId');
        $lessonsCount = Lesson::find()
            ->isConfirmed()
            ->notDeleted()
            ->location($locationId)
            ->andWhere(['NOT IN', 'lesson.status', Lesson::STATUS_CANCELED])
            ->between($fromDate, $toDate)
            ->count();
        $students = Student::find()
			->notDeleted()
            ->groupBy('student.id')
            ->active($from, $to)
            ->count();

        $completedPrograms = [];
        $programs = Lesson::find()
                    ->select(['sum(TIME_TO_SEC(lesson.duration)) as hours, program.name as program_name, lesson.type'])
                    ->joinWith(['course' => function ($query) {
                        $query->joinWith('program')
                            ->confirmed()
                            ->notDeleted();
                    }])
                    ->between($fromDate, $toDate)
                    ->privateLessons()
                    ->scheduledOrRescheduled()
                    ->isConfirmed()
                    ->notDeleted()
                    ->location($locationId)
                    ->groupBy(['course.programId'])
                    ->all();
        foreach ($programs as $program) {
            $completedProgram = [];
            $completedProgram['name'] = $program->program_name;
            $completedProgram['y'] = $program->hours / 3600;
            array_push($completedPrograms, $completedProgram);
        }

        $enrolmentGains = [];
        $allEnrolments = Enrolment::find()
            ->select(['COUNT(enrolment.id) as enrolmentCount, program.name as programName'])
            ->joinWith(['course' => function ($query) use ($locationId, $fromDate, $toDate) {
                $query->joinWith(['program' => function ($query) {
                }])
                ->confirmed()
                ->notDeleted()
                ->location($locationId)
                ->between($fromDate, $toDate);
            }])
            ->groupBy(['course.programId'])
            ->all();
        foreach ($allEnrolments as $allEnrolment) {
            $enrolmentGain = [];
            $enrolmentGain['name'] = $allEnrolment->programName;
            $enrolmentGain['y'] = round($allEnrolment->enrolmentCount, 2);
            array_push($enrolmentGains, $enrolmentGain);
        }
        $enrolmentLosses = [];
        $allLossEnrolments = Enrolment::find()
            ->select(['COUNT(enrolment.id) as enrolmentCount, program.name as programName'])
            ->joinWith(['course' => function ($query) use ($locationId, $fromDate, $toDate) {
                $query->joinWith(['program' => function ($query) {
                }])
                ->confirmed()
                ->notDeleted()
                ->location($locationId)
                ->betweenEndDate($fromDate, $toDate);
                
            }])
            ->andWhere(['>=', 'DATE(course.endDate)', (new \DateTime())->format('Y-m-d')])
            ->isConfirmed()
            ->isRegular()
            ->groupBy(['course.programId'])
            ->all();
        foreach ($allLossEnrolments as $allLossEnrolment) {
            $enrolmentLoss = [];
            $enrolmentLoss['name'] = $allLossEnrolment->programName;
            $enrolmentLoss['y'] = round($allLossEnrolment->enrolmentCount);
            array_push($enrolmentLosses, $enrolmentLoss);
        }

        $enrolmentGainCount = Enrolment::find()
            ->joinWith(['course' => function ($query) use ($locationId, $fromDate, $toDate) {
                $query->joinWith(['program' => function ($query) {
                }])
                ->confirmed()
                ->notDeleted()
                ->location($locationId)
                ->between($fromDate, $toDate);
            }])
            ->count();
        $enrolmentLossCount = Enrolment::find()
            ->joinWith(['course' => function ($query) use ($locationId, $fromDate, $toDate) {
                $query->joinWith(['program' => function ($query) {
                }])
                ->confirmed()
                ->notDeleted()
                ->location($locationId)
                ->betweenEndDate($fromDate, $toDate);
            }])
            ->andWhere(['>=', 'DATE(course.endDate)', (new \DateTime())->format('Y-m-d')])
            ->isConfirmed()
            ->isRegular()
            ->count();
        $instructionHours = Lesson::find()
            ->between($fromDate, $toDate)
            ->privateLessons()
            ->scheduledOrRescheduled()
            ->isConfirmed()
            ->notDeleted()
            ->location($locationId)
            ->sum('TIME_TO_SEC(lesson.duration)');
        $instructionHoursCount = $instructionHours / 3600;
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'enrolments' => $enrolments,
            'groupEnrolments' => $groupEnrolments,
            'students' => $students,
            'completedPrograms' => $completedPrograms,
            'enrolmentGains' => $enrolmentGains,
            'enrolmentLosses' => $enrolmentLosses,
            'enrolmentGainCount' => $enrolmentGainCount,
            'enrolmentLossCount' => $enrolmentLossCount,
            'lessonsCount' => $lessonsCount,
            'instructionHoursCount' => $instructionHoursCount
        ]);
    }

    public function canView()
    {
        return Yii::$app->user->can('manageMonthlyRevenue') ||
            Yii::$app->user->can('manageEnrolmentGains') ||
            Yii::$app->user->can('manageEnrolmentLosses') ||
            Yii::$app->user->can('manageInstructionHours');
    }
}
