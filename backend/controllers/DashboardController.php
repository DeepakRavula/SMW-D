<?php

namespace backend\controllers;

use Yii;
use common\models\Invoice;
use common\models\Enrolment;
use common\models\Payment;

class DashboardController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $fromDate =  \DateTime::createFromFormat('d-m-Y', date('1-m-Y'));
		$toDate =  \DateTime::createFromFormat('d-m-Y', date('t-m-Y'));
        $currentDate =  \DateTime::createFromFormat('d-m-Y', date('d-m-Y'));
        $invoiceTotal = Invoice::find()
                        ->where(['between','date', $fromDate->format('Y-m-d'), $toDate->format('Y-m-t')])
                        ->sum('subTotal');
        $invoiceTaxTotal = Invoice::find()
                        ->where(['between','date', $fromDate->format('Y-m-d'), $toDate->format('Y-m-t')])
                        ->sum('tax');
        $enrolments = Enrolment::find()
                    ->where(['>','renewal_date', $currentDate->format('Y-m-d')])
                    ->count('id');
        $payments = Payment::find()
                    ->where(['between','date', $fromDate->format('Y-m-d'), $toDate->format('Y-m-t')])
                    ->sum('amount');
        $students = Enrolment::find()
                    ->where(['>','renewal_date', $currentDate->format('Y-m-d')])
                    ->count('distinct student_id');
        
        return $this->render('index', ['invoiceTotal' => $invoiceTotal, 'invoiceTaxTotal' => $invoiceTaxTotal, 'enrolments' => $enrolments, 'payments' => $payments, 'students' => $students]);
    }

}
