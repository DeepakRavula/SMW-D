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
        $currentDate = new \DateTime();
        $searchModel->fromDate = $currentDate->format('1-m-Y');
		$searchModel->toDate = $currentDate->format('t-m-Y');
        $request = Yii::$app->request;
        if($searchModel->load($request->get())){
            $dashboardRequest = $request->get('DashboardSearch');
            $searchModel->fromDate = $dashboardRequest['fromDate'];
            $searchModel->toDate = $dashboardRequest['toDate'];
        }
        $searchModel->fromDate =  \DateTime::createFromFormat('d-m-Y', $searchModel->fromDate);
		$searchModel->toDate =  \DateTime::createFromFormat('d-m-Y', $searchModel->toDate);       
        $locationId = Yii::$app->session->get('location_id');
        $invoiceTotal = Invoice::find()
                        ->where(['location_id' => $locationId])
                        ->andWhere(['between','date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
                        ->sum('subTotal');
        $invoiceTaxTotal = Invoice::find()
                        ->where(['location_id' => $locationId])
                        ->andWhere(['between','date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
                        ->sum('tax');
        $enrolments = Enrolment::find()
                    ->where(['location_id' => $locationId])
                    ->andWhere(['>','renewal_date', $currentDate->format('Y-m-d')])
                    ->count('id');
        $payments = Payment::find()
                    ->joinWith(['invoice i' => function($payments) use($locationId) {                        
                            $payments->where(['i.location_id' => $locationId]);                        
                    }])
                    ->andWhere(['between','payment.date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
                    ->sum('payment.amount');
        $students = Enrolment::find()
                    ->where(['location_id' => $locationId])
                    ->andWhere(['>','renewal_date', $currentDate->format('Y-m-d')])
                    ->count('distinct student_id');
        
        return $this->render('index', ['searchModel' => $searchModel, 'invoiceTotal' => $invoiceTotal, 'invoiceTaxTotal' => $invoiceTaxTotal, 'enrolments' => $enrolments, 'payments' => $payments, 'students' => $students]);
    }

}
