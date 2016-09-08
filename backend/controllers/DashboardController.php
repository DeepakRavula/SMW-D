<?php

namespace backend\controllers;

use Yii;
use common\models\Invoice;
use common\models\Enrolment;
use common\models\Payment;
use backend\models\search\DashboardSearch;

class DashboardController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $searchModel = new DashboardSearch();
        $searchModel->fromDate =  date('1-m-Y');
		$searchModel->toDate =  date('t-m-Y');
        $request = Yii::$app->request;
        if($searchModel->load($request->get())){
            $dashboardRequest = $request->get('DashboardSearch');
            $searchModel->fromDate = $dashboardRequest['fromDate'];
            $searchModel->toDate = $dashboardRequest['toDate'];
        }
        $searchModel->fromDate =  \DateTime::createFromFormat('d-m-Y', $searchModel->fromDate);
		$searchModel->toDate =  \DateTime::createFromFormat('d-m-Y', $searchModel->toDate);
        $currentDate =  \DateTime::createFromFormat('d-m-Y', date('d-m-Y'));
        $invoiceTotal = Invoice::find()
                        ->where(['between','date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-t')])
                        ->sum('subTotal');
        $invoiceTaxTotal = Invoice::find()
                        ->where(['between','date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-t')])
                        ->sum('tax');
        $enrolments = Enrolment::find()
                    ->where(['>','renewal_date', $currentDate->format('Y-m-d')])
                    ->count('id');
        $payments = Payment::find()
                    ->where(['between','date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-t')])
                    ->sum('amount');
        $students = Enrolment::find()
                    ->where(['>','renewal_date', $currentDate->format('Y-m-d')])
                    ->count('distinct student_id');
        
        return $this->render('index', ['searchModel' => $searchModel, 'invoiceTotal' => $invoiceTotal, 'invoiceTaxTotal' => $invoiceTaxTotal, 'enrolments' => $enrolments, 'payments' => $payments, 'students' => $students]);
    }

}
