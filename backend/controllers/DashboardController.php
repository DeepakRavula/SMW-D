<?php

namespace backend\controllers;

use Yii;
use common\models\Invoice;
use common\models\Lesson;
use common\models\Enrolment;
use common\models\Payment;
use common\models\GroupCourse;
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
                        ->andWhere(['between', 'date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
                        ->sum('subTotal');
        $invoiceTaxTotal = Invoice::find()
                        ->where(['location_id' => $locationId])
                        ->andWhere(['between','date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
                        ->sum('tax');
        $enrolments = Enrolment::find()
                    ->where(['location_id' => $locationId])                
                    ->andWhere(['>=','renewal_date', $currentDate->format('Y-m-d')])
                    ->count('student_id');
        $groupEnrolments = GroupCourse::find()
                    ->joinWith(['groupEnrolments'])
                    ->where(['location_id' => $locationId])
                    ->andWhere(['>=', 'end_date', $currentDate->format('Y-m-d')])
                    ->count('group_enrolment.student_id');
        $payments = Payment::find()
                    ->joinWith(['invoice i' => function($payments) use($locationId) {                        
                            $payments->where(['i.location_id' => $locationId]);                        
                    }])
                    ->andWhere(['between','payment.date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
                    ->sum('payment.amount');
         $students = Student::find()
                    ->joinWith(['groupEnrolments'=>function($query) {
                         $query->joinWith('groupCourse'); 
                    }])
                    ->joinWith('enrolment')
                    ->andWhere(['OR',
                        ['enrolment.location_id' => $locationId],['group_course.location_id' => $locationId],
                        ])
                    ->andWhere(['OR',[
                        'NOT', ['enrolment.student_id' => null]],['NOT', ['group_enrolment.student_id' => null]
                        ]]) 
                    ->andWhere(['OR',
                        ['>=','enrolment.renewal_date', $currentDate->format('Y-m-d')],
                        ['>=','group_course.end_date', $currentDate->format('Y-m-d')]
                        ])
                    ->distinct(['group_enrolment.student_id','enrolment.student_id'])
                    ->count();
        $programsHours = Lesson::find()
                    ->select(['sum(enrolment.duration) as hours'])
                    ->joinWith('enrolment')
                    ->where(['enrolment.location_id' => $locationId])
                    ->andWhere(['between','lesson.date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
                    ->where(['lesson.status' => Lesson::STATUS_COMPLETED])
                    ->all();
        $totalHours = floor($programsHours[0]->hours / 3600);
        $completedPrograms = [];            
        $programs = Lesson::find()
                    ->select(['sum(enrolment.duration) as hours, program.name as program_name'])
                    ->joinWith(['enrolment' => function($query) use($locationId) {                     
                        $query->where(['enrolment.location_id' => $locationId]) ;                   
                        $query->joinWith(['program' => function($query){   
                        }]);
                    }])
                    ->andWhere(['between','lesson.date', $searchModel->fromDate->format('Y-m-d'), $searchModel->toDate->format('Y-m-d')])
                    ->where(['lesson.status' => Lesson::STATUS_COMPLETED])
                    ->groupBy(['enrolment.program_id'])
                    ->all();
        foreach($programs as $program){
            $array = array();
            $array['name']  =  $program->program_name;
            $array['y'] =   floor(( (floor($program->hours / 3600)) / $totalHours ) * 100) ;//$program->hours;
            array_push($completedPrograms, $array);
        }
        
        return $this->render('index', ['searchModel' => $searchModel, 'invoiceTotal' => $invoiceTotal, 'invoiceTaxTotal' => $invoiceTaxTotal, 'enrolments' => $enrolments, 'groupEnrolments' => $groupEnrolments, 'payments' => $payments, 'students' => $students, 'completedPrograms' => $completedPrograms]);
    }

}
