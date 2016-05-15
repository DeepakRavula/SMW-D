<?php

namespace backend\controllers;

use Yii;
use common\models\Qualification;
use common\models\TeacherAvailability;
use backend\models\search\QualificationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * QualificationController implements the CRUD actions for Qualification model.
 */
class ScheduleController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Qualification models.
     * @return mixed
     */
    public function actionIndex()
    {
        /* $teacherAvailability = ArrayHelper::map(TeacherAvailability::find()->all(), 'id', 'teacher_id as name');*/
        $teacherAvailability = (new \yii\db\Query())
            ->select(['ta.teacher_id', 'concat(up.firstname,\' \',up.lastname) as name'])
            ->from('teacher_availability_day ta')
            ->join('Join', 'user_profile up', 'up.user_id = ta.teacher_id')
            ->where('ta.location_id = :location_id AND ta.day = DATE_FORMAT(now(),\'%w\')', [':location_id'=>1])
            ->all();
        
        
		return $this->render('index', ['teacherAvailability'=>$teacherAvailability]);
    }

}