<?php

namespace backend\controllers;

use Yii;
use common\models\Location;
use common\models\Lesson;
use common\models\Enrolment;
use common\models\Student;
use backend\models\search\DashboardSearch;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\UserLocation;

class DashboardController extends \common\components\backend\BackendController
{
    public function actionIndex()
    {
		$roles = ArrayHelper::getColumn(
			Yii::$app->authManager->getRolesByUser(Yii::$app->user->id),
				'name'
			);
		$role = end($roles);
                if($role !== User::ROLE_ADMINISTRATOR && $role !== User::ROLE_OWNER)
                {
                   return $this->redirect(['schedule/index']);
                }
		if ($role !== User::ROLE_ADMINISTRATOR) {
			$userLocation = UserLocation::findOne(['user_id' => Yii::$app->user->id]);
			Yii::$app->session->set('location_id', $userLocation->location_id);
		}
        $searchModel = new DashboardSearch();
        $currentDate = new \DateTime();
        $searchModel->fromDate = $currentDate->format('M 1,Y');
        $searchModel->toDate = $currentDate->format('M t,Y');
        $searchModel->dateRange = $searchModel->fromDate.' - '.$searchModel->toDate;
        $request = Yii::$app->request;
        if ($searchModel->load($request->get())) {
            $dashboardRequest = $request->get('DashboardSearch');
            $searchModel->dateRange = $dashboardRequest['dateRange'];
        }
        $toDate = $searchModel->toDate;
        if ($toDate > $currentDate) {
            $toDate = $currentDate;
        }
        $locationId = Location::findOne(['slug' => Yii::$app->language])->id;
        
        $enrolments = Enrolment::find()
			->joinWith(['course' => function($query) use($locationId, $searchModel) {
				$query->joinWith(['program' => function($query) {
					$query->privateProgram();
				}])
				->confirmed()
				->location($locationId)
				->between($searchModel->fromDate, $searchModel->toDate);
			}])
            ->count('studentId');

        $groupEnrolments = Enrolment::find()
            ->joinWith(['course' => function($query) use($locationId, $searchModel) {
				$query->joinWith(['program' => function($query) {
					$query->group();
				}])
				->confirmed()
				->location($locationId)
				->between($searchModel->fromDate, $searchModel->toDate);
			}])
            ->count('studentId');
		$lessonsCount = Lesson::find()
			->isConfirmed()
			->notDeleted()
			->location($locationId)
			->andWhere(['NOT IN', 'lesson.status', Lesson::STATUS_CANCELED])
			->between($searchModel->fromDate, $searchModel->toDate)
			->count();
		
        $students = Student::find()
            ->notDeleted()
            ->joinWith(['enrolment' => function ($query) use ($locationId, $searchModel) {
                $query->joinWith(['course' => function ($query) use ($locationId, $searchModel) {
                $query->confirmed()
					->location($locationId)
					->between($searchModel->fromDate, $searchModel->toDate);
                }]);
            }])
			->active()
            ->distinct(['enrolment.studentId'])
            ->count();

        $completedPrograms = [];
        $programs = Lesson::find()
                    ->select(['sum(course_schedule.duration) as hours, program.name as program_name'])
                    ->joinWith(['course' => function ($query) use ($locationId) {
                        $query->joinWith('program')
							->joinWith('courseSchedule')
                            ->where(['course.locationId' => $locationId]);
                    }])
                    ->andWhere(['between', 'lesson.date', $searchModel->fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
                    ->andWhere(['not', ['lesson.status' => [Lesson::STATUS_CANCELED]]])
					->isConfirmed()
                    ->notDeleted()
                    ->groupBy(['course.programId'])
                    ->all();
        foreach ($programs as $program) {
            $completedProgram = [];
            $completedProgram['name'] = $program->program_name;
            $completedProgram['y'] = $program->hours / 6000;
            array_push($completedPrograms, $completedProgram);
        }

		$enrolmentGains = [];
        $allEnrolments = Enrolment::find()
            ->select(['COUNT(enrolment.id) as enrolmentCount, program.name as programName'])
            ->joinWith(['course' => function($query) use($locationId, $searchModel) {
				$query->joinWith(['program' => function($query) {
				}])
				->confirmed()
				->location($locationId)
				->between($searchModel->fromDate, $searchModel->toDate);
			}])
            ->groupBy(['course.programId'])
            ->all();
        foreach ($allEnrolments as $allEnrolment) {
            $enrolmentGain = [];
            $enrolmentGain['name'] = $allEnrolment->programName;
            $enrolmentGain['y'] = round($allEnrolment->enrolmentCount,2);
            array_push($enrolmentGains, $enrolmentGain);
        }
		$enrolmentLosses = [];
        $allLossEnrolments = Enrolment::find()
            ->select(['COUNT(enrolment.id) as enrolmentCount, program.name as programName'])
            ->joinWith(['course' => function($query) use($locationId, $searchModel) {
				$query->joinWith(['program' => function($query) {
				}])
				->confirmed()
				->location($locationId)
				->betweenEndDate($searchModel->fromDate, $searchModel->toDate);
			}])
            ->groupBy(['course.programId'])
            ->all();
        foreach ($allLossEnrolments as $allLossEnrolment) {
            $enrolmentLoss = [];
            $enrolmentLoss['name'] = $allLossEnrolment->programName;
            $enrolmentLoss['y'] = round($allLossEnrolment->enrolmentCount);
            array_push($enrolmentLosses, $enrolmentLoss);
        }

		$enrolmentGainCount = Enrolment::find()
            ->joinWith(['course' => function($query) use($locationId, $searchModel) {
				$query->joinWith(['program' => function($query) {
				}])
				->confirmed()
				->location($locationId)
				->between($searchModel->fromDate, $searchModel->toDate);
			}])
            ->count();
		$enrolmentLossCount = Enrolment::find()
            ->joinWith(['course' => function($query) use($locationId, $searchModel) {
				$query->joinWith(['program' => function($query) {
				}])
				->confirmed()
				->location($locationId)
				->betweenEndDate($searchModel->fromDate, $searchModel->toDate);
			}])
            ->count();
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
			'lessonsCount' => $lessonsCount
		]);
    }
}
