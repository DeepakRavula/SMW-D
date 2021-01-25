<?php

use common\models\Note;
use common\models\PrivateLesson;
use common\models\User;
use kartik\select2\Select2Asset;
use kartik\time\TimePickerAsset;
use yii\bootstrap\Modal;
use yii\bootstrap\Tabs;
use yii\helpers\Url;
Select2Asset::register($this);
TimePickerAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\models\Student */

?>
<script src="/plugins/bootbox/bootbox.min.js"></script>
<div style="min-height: 836px;background-color: #ecf0f5;z-index: 800;">
<section class="content-header">
                <h1>
                                          
                    <div class="pull-left course-icon m-r-10">
                        <a href="/admin/training-location/enrolment/index">Lesson</a>  / 
<?= $model->course->program->name; ?><span class="m-l-10"></span>                    </div>
					 
                    					  
                </h1>

            </section>
                      
                                  
<br>

<div class="row" style="padding:10px;">
	<div class="col-md-6">
		<?=$this->render('course_info_view', [
            'model' => $model,
        ]);?>      
    </div>
    <div class="col-md-6">
		<?=$this->render('student_info_view', [
            'model' => $model,
        ]);?>      
    </div>
</div>

</div>


