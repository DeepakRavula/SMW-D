<?php

namespace backend\controllers;

use Yii;
use common\models\Invoice;
use common\models\InvoiceLineItem;
use common\models\Lesson;
use common\models\Enrolment;
use common\models\Payment;
use common\models\Student;
use backend\models\search\DashboardSearch;

class DashboardController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $searchModel = new DashboardSearch();
        $currentDate = new \DateTime();
        $searchModel->fromDate = $currentDate->format('1-m-Y');
        $searchModel->toDate = $currentDate->format('t-m-Y');
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
        $locationId = Yii::$app->session->get('location_id');
        $invoiceTotal = Invoice::find()
                        ->where(['location_id' => $locationId, 'type' => Invoice::TYPE_INVOICE])
                        ->andWhere(['between', 'date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
						->notDeleted()
                        ->sum('subTotal');
        $invoiceTaxTotal = Invoice::find()
                        ->where(['location_id' => $locationId, 'type' => Invoice::TYPE_INVOICE])
						->andWhere(['status' => [Invoice::STATUS_PAID, Invoice::STATUS_CREDIT]])
                        ->andWhere(['between', 'date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
						->notDeleted()
                        ->sum('tax');
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

        $payments = Payment::find()
                    ->joinWith(['invoice i' => function ($query) use ($locationId) {
                        $query->where(['i.location_id' => $locationId, 'type' => Invoice::TYPE_INVOICE]);
                    }])
                    ->andWhere(['between', 'payment.date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
                    ->sum('payment.amount');

        $royaltyPayment = InvoiceLineItem::find()
                    ->joinWith(['invoice i' => function ($query) use ($locationId) {
                        $query->where(['i.location_id' => $locationId, 'type' => Invoice::TYPE_INVOICE]);
                    }])
					->andWhere(['status' => [Invoice::STATUS_PAID, Invoice::STATUS_CREDIT]])
                    ->andWhere(['between', 'i.date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
                    ->andWhere(['invoice_line_item.isRoyalty' => false])
                    ->sum('invoice_line_item.amount');

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
                    ->andWhere(['not', ['lesson.status' => [Lesson::STATUS_CANCELED, Lesson::STATUS_DRAFTED]]])
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
			'invoiceTotal' => $invoiceTotal,
			'invoiceTaxTotal' => $invoiceTaxTotal,
			'enrolments' => $enrolments,
			'groupEnrolments' => $groupEnrolments,
			'payments' => $payments,
			'students' => $students,
			'completedPrograms' => $completedPrograms,
			'royaltyPayment' => $royaltyPayment,
			'enrolmentGains' => $enrolmentGains,	
			'enrolmentLosses' => $enrolmentLosses,	
			'enrolmentGainCount' => $enrolmentGainCount,
			'enrolmentLossCount' => $enrolmentLossCount,
		]);
    }
}
