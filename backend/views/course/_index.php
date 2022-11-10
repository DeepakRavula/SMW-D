<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use common\models\Course;
use common\models\CourseSchedule;
use yii\helpers\Html;

?>

<div id="error-notification" style="display: none;" class="alert-danger alert fade in"></div>

<div class="clearfix"></div>	
<?php $form = ActiveForm::begin([
    'id' => 'group-course-form',
        'action' => Url::to(['course/create']),
]); ?>
	    <div id="step-1">
	       <?= $this->render('/course/_form', [
                'model' => new Course(),
                'form' => $form,
            ]);?>
                <div class="form-group pull-right">
		<?= Html::a('Cancel', '#', ['class' => 'btn btn-default group-course-cancel']); ?>
		<button class="nextBtn btn btn-info m-l-20 step1-next" type="button" >Next</button>
                </div>
      	    </div>
	    <div id="step-2">
                    <div class="col-md-12">
	       <?= $this->render('/course/_form-add-lesson', [
                'courseSchedule' => [new CourseSchedule()],
                'form' => $form,
            ]);?> 
                        
                    </div>
                <div class="col-md-12">
			<div class="pull-right">
				<?php
                    echo Html::a('Cancel', [''], ['class' => 'btn btn-default group-course-cancel']);
                                        echo Html::submitButton(Yii::t('backend', 'Preview Lessons'), ['id' => 'group-course-save', 'class' => 'btn btn-info m-l-10', 'name' => 'signup-button'])
                ?>
			</div>
                    <div class="form-group pull-left">
		<button class="step2-back btn btn-info" type="button" >Back</button>
    </div>
		</div>
                </div>
	    
<?php ActiveForm::end(); ?>

<script>
$(document).ready(function () {
    $('#step-2').hide();
    $('#group-course-create-modal .modal-dialog').css({'width': '400px'});
 $(document).on('click', '.step1-next', function() {
		$('#step-1').hide();
                $('#group-course-create-modal .modal-dialog').css({'width': '600px'});
		$('#step-2').show();
		return false;
	});
	$(document).on('click', '.step2-back', function() {
		$('#step-2').hide();
                $('#group-course-create-modal .modal-dialog').css({'width': '400px'});
		$('#step-1').show();
		return false;
	});
	  $(document).on('click', '.group-course-cancel', function() {                  
        $('#group-course-create-modal').modal('hide');
        return false;
    });
});
</script>
