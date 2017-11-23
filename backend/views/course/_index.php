<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use common\models\Course;
use common\models\CourseSchedule;
use yii\helpers\Html;

?>

<div id="error-notification" style="display: none;" class="alert-danger alert fade in"></div>
			<div class="requestwizard">
				<div class="requestwizard-row setup-panel">
					<div class="requestwizard-step">
			            <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
			            <p>Program</p>
			        </div>
			        <div class="requestwizard-step">
			            <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
			            <p>Lesson</p>
			        </div>
			       </div>
			</div>
<div class="clearfix"></div>	
<?php $form = ActiveForm::begin([
	'id' => 'group-course-form',
        'action' => Url::to(['course/create']),
]); ?>
	    <div class="row setup-content" id="step-1">
	       <?= $this->render('/course/_form', [
				'model' => new Course(), 
				'form' => $form,
			]);?>
	    </div>
	    <div class="row setup-content" id="step-2">
                    <div class="col-md-10 m-l-20">
	       <?= $this->render('/course/_form-add-lesson', [
				'courseSchedule' => [new CourseSchedule()], 
				'form' => $form,
			]);?> 
                        
                    </div>
                <div class="col-md-12">
			<div class="pull-right">
				<?php
					echo Html::a('Cancel', [''], ['class' => 'btn btn-default']);
                                        echo Html::submitButton(Yii::t('backend', 'Preview Lessons'), ['id' => 'group-course-save', 'class' => 'btn btn-info m-l-10', 'name' => 'signup-button'])
				?>
			</div>
		</div>
                </div>
	    
<?php ActiveForm::end(); ?>

<script>
$(document).ready(function () {
    var navListItems = $('div.setup-panel div a'),
            allWells = $('.setup-content'),
            allNextBtn = $('.nextBtn');

    allWells.hide();

    navListItems.click(function (e) {
        e.preventDefault();
        var $target = $($(this).attr('href')),
                $item = $(this);

        if (!$item.hasClass('disabled')) {
            navListItems.removeClass('btn-primary').addClass('btn-default');
            $item.addClass('btn-primary');
            allWells.hide();
            $target.show();
            $target.find('input:eq(0)').focus();
        }
    });

    allNextBtn.click(function(){
        var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
            curInputs = curStep.find("input[type='text'],input[type='url']"),
            isValid = true;
 $(".form-group").removeClass("has-error");
        for(var i=0; i<curInputs.length; i++){
            if (!curInputs[i].validity.valid){
                isValid = false;
                $(curInputs[i]).closest(".form-group").addClass("has-error");
            }
        }

        if (isValid)
            nextStepWizard.removeAttr('disabled').trigger('click');
    });

    $('div.setup-panel div a.btn-primary').trigger('click');
});
</script>
