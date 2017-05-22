<?php

namespace backend\controllers;

use Yii;
use common\models\Invoice;
use common\models\InvoiceLineItem;
use common\models\Lesson;
use common\models\Enrolment;
use common\models\Payment;
use common\models\Program;
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
                    ->notDeleted()
                    ->program($locationId, $currentDate)
                    ->where([
						'program.type' => Program::TYPE_PRIVATE_PROGRAM,
						'enrolment.isConfirmed' => true,
					])
                    ->count('studentId');

        $groupEnrolments = Enrolment::find()
                    ->notDeleted()
                    ->program($locationId, $currentDate)
                    ->where([
						'program.type' => Program::TYPE_GROUP_PROGRAM,
						'enrolment.isConfirmed' => true,
					])
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
            ->joinWith(['enrolment' => function ($query) use ($locationId, $currentDate) {
                $query->joinWith(['course' => function ($query) use ($locationId, $currentDate) {
                    $query->andWhere(['locationId' => $locationId])
                        ->andWhere(['NOT', ['studentId' => null]])
                        ->andWhere(['>=', 'endDate', $currentDate->format('Y-m-d')]);
                }])
				->where(['enrolment.isConfirmed' => true]);
            }])
			->active()
            ->distinct(['enrolment.studentId'])
            ->count();

        $completedPrograms = [];
        $programs = Lesson::find()
                    ->select(['sum(course.duration) as hours, program.name as program_name'])
                    ->joinWith(['course' => function ($query) use ($locationId) {
                        $query->where(['course.locationId' => $locationId]);
                        $query->joinWith(['program' => function ($query) {
                        }]);
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

		$enrolments = [];
        $programs = Enrolment::find()
                    ->notDeleted()
                    ->program($locationId, $currentDate)
                    ->where([
						'program.type' => Program::TYPE_PRIVATE_PROGRAM,
						'enrolment.isConfirmed' => true,
					])
                    ->groupBy(['course.programId'])
                    ->all();
        foreach ($programs as $program) {
            $completedProgram = [];
            $completedProgram['name'] = $program->program_name;
            $completedProgram['y'] = $program->hours / 6000;
            array_push($completedPrograms, $completedProgram);
        }

        return $this->render('index', ['searchModel' => $searchModel, 'invoiceTotal' => $invoiceTotal, 'invoiceTaxTotal' => $invoiceTaxTotal, 'enrolments' => $enrolments, 'groupEnrolments' => $groupEnrolments, 'payments' => $payments, 'students' => $students, 'completedPrograms' => $completedPrograms, 'royaltyPayment' => $royaltyPayment]);
    }
}
