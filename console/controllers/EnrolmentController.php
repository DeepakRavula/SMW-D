<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\Course;

class EnrolmentController extends Controller
{
    public function actionAutoRenewal()
    {
        $course = Course::find()
                ->confirmed()
                ->all();
    }
}