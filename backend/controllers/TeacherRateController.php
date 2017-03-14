<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use common\models\User;
use common\models\TeacherRate;

/**
 * TeacherAvailabilityController implements the CRUD actions for TeacherAvailability model.
 */
class TeacherRateController extends Controller
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

    public function actionUpdate($id)
    {
        $model = User::findOne(['id' => $id]);
		$post = Yii::$app->request->post();
		$rate = $post['value'];
        if($post['name'] === 'privateLessonHourlyRate') {
			if(!empty($model->teacherPrivateLessonRate)) {
				$model->teacherPrivateLessonRate->hourlyRate = $rate; 
				$model->teacherPrivateLessonRate->save();
				$result = $model->teacherPrivateLessonRate->hourlyRate; 
			} else {
				$teacherRate = new TeacherRate();
				$teacherRate->teacherId = $model->id;	
				$teacherRate->hourlyRate = $rate;
				$teacherRate->type = TeacherRate::TYPE_PRIVATE_LESSON;	
				$teacherRate->save();
			}
		} else {
			if(!empty($model->teacherGroupLessonRate)) {
				$model->teacherGroupLessonRate->hourlyRate = $rate; 
				$model->teacherGroupLessonRate->save();
				$result = $model->teacherGroupLessonRate->hourlyRate; 
			} else {
				$teacherRate = new TeacherRate();
				$teacherRate->teacherId = $model->id;	
				$teacherRate->hourlyRate = $rate;
				$teacherRate->type = TeacherRate::TYPE_GROUP_LESSON;	
				$teacherRate->save();
			}
		}
    }
}
