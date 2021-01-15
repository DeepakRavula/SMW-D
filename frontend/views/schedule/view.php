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
<br>

<div class="row">
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




