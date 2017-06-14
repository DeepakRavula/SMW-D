<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use yii\helpers\Url;
use common\models\Note;
use common\models\Lesson;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
$this->title = 'Lessons / Lesson Details';
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['index', 'LessonSearch[type]' => Lesson::TYPE_PRIVATE_LESSON], ['class' => 'go-back text-add-new f-s-14 m-t-0 m-r-10']);
?>
<div class="row">
	<div class="col-md-6">
	<div class="box box-default">
          <div class="box-header with-border">
              <h3 class="box-title">Details</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool"><i class="fa fa-pencil"></i></button>
              </div>
            </div>
        </div>
	<div class="box box-default">
		 <div class="box-header with-border">
              <h3 class="box-title">Schedule</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" ><i class="fa fa-pencil"></i></button>
              </div>
            </div>
        </div>
	</div>
	<div class="col-md-6">
		<div class="box box-default">
          <div class="box-header with-border">
              <h3 class="box-title">Student</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" ><i class="fa fa-pencil"></i></button>
              </div>
            </div>
        </div>
		<div class="box box-default">
          <div class="box-header with-border">
              <h3 class="box-title">Attendance</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool"><i class="fa fa-pencil"></i></button>
              </div>
            </div>
        </div>
	</div>
	</div>