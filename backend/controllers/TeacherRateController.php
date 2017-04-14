<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use common\models\User;
use common\models\TeacherRate;
use yii\web\Response;

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
			[
				'class' => 'yii\filters\ContentNegotiator',
				'only' => ['update'],
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
				],
        	],
        ];
    }

    public function actionUpdate($id)
    {
        $model = User::findOne(['id' => $id]);
		if ($model->load(\Yii::$app->getRequest()->getBodyParams(), '') && $model->hasEditable) {
			if(isset($model->groupLessonHourlyRate)) {
				if(!empty($model->teacherGroupLessonRate)) {
					$model->teacherGroupLessonRate->hourlyRate = $model->groupLessonHourlyRate; 
					$model->teacherGroupLessonRate->save();
					$result = $model->teacherGroupLessonRate->hourlyRate; 
				} else {
					$teacherRate = new TeacherRate();
					$teacherRate->teacherId = $model->id;	
					$teacherRate->hourlyRate = $model->groupLessonHourlyRate;
					$teacherRate->type = TeacherRate::TYPE_GROUP_LESSON;	
					$teacherRate->save();
					$result = $teacherRate->hourlyRate; 
				}	
			}
			if(isset($model->privateLessonHourlyRate)) {
				if(!empty($model->teacherPrivateLessonRate)) {
					$model->teacherPrivateLessonRate->hourlyRate = $model->privateLessonHourlyRate; 
					$model->teacherPrivateLessonRate->save();
					$result = $model->teacherPrivateLessonRate->hourlyRate; 
				} else {
					$teacherRate = new TeacherRate();
					$teacherRate->teacherId = $model->id;	
					$teacherRate->hourlyRate = $model->privateLessonHourlyRate;
					$teacherRate->type = TeacherRate::TYPE_PRIVATE_LESSON;	
					$teacherRate->save();
					$result = $teacherRate->hourlyRate; 
				}
			}
            return ['output' => $result, 'message' => ''];
        }
    }
}
