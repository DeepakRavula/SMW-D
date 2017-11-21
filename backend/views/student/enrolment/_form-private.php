<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use common\models\LocationAvailability;
use common\models\Course;
use common\models\CourseSchedule;
use backend\models\discount\EnrolmentDiscount;
?>
<style>
	.requestwizard-modal{
	background: rgba(255, 255, 255, 0.8);
	box-shadow: rgba(0, 0, 0, 0.3) 20px 20px 20px;
}
.requestwizard-step p {
    margin-top: 10px;
}

.requestwizard-row {
    display: table-row;
}

.requestwizard {
    display: table;
    width: 100%;
    position: relative;
}

.requestwizard-step button[disabled] {
    opacity: 1 !important;
    filter: alpha(opacity=100) !important;
}
.requestwizard-row:before {
    top: 14px;
    bottom: 0;
    position: absolute;
    content: " ";
    width: 100%;
    height: 1px;
    background-color: #ccc;
    z-order: 0;

}

.requestwizard-step {
    display: table-cell;
    text-align: center;
    position: relative;
}

.btn-circle {
  width: 30px;
  height: 30px;
  text-align: center;
  padding: 6px 0 6px 0;
  font-size: 12px;
  line-height: 1.428571429;
  border-radius: 15px;
}
</style>
<div class="user-create-form">

		<div class="requestwizard">
				<div class="requestwizard-row setup-panel">
					<div class="requestwizard-step">
			            <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
			            <p>Program</p>
			        </div>
			        <div class="requestwizard-step">
			            <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
			            <p>Teacher</p>
			        </div>
		    	</div>
			</div>
<?php
$form = ActiveForm::begin([
		'id' => 'enrolment-form',
		'layout' => 'horizontal',
		'action' => Url::to(['student/enrolment', 'id' => $student->id]),
	]);
?>	
	    <div class="row setup-content" id="step-1">
	    <?= $this->render('_step-one', [
			'model' => new Course(), 
			'courseSchedule' => new CourseSchedule(),
			'paymentFrequencyDiscount' => new EnrolmentDiscount(),
			'multipleEnrolmentDiscount' => new EnrolmentDiscount(),
			'student' => $model,
			'form' => $form
		]);?> 
	    </div>
	    <div class="row setup-content" id="step-2">
			<?= $this->render('_step-two', [
				'model' => new Course(), 
				'courseSchedule' => new CourseSchedule(),
				'form' => $form
		]);?>  
	    </div>
<?php ActiveForm::end(); ?>
		</div>

<script>
var enrolment = {
	fetchProgram: function(duration, programId, paymentFrequencyDiscount, multiEnrolmentDiscount, programRate) {
		var params = $.param({duration: duration, id: programId, paymentFrequencyDiscount: paymentFrequencyDiscount,
			multiEnrolmentDiscount: multiEnrolmentDiscount, rate: programRate });
		$.ajax({
			url: '<?= Url::to(['student/fetch-program-rate']); ?>?' + params,
			type: 'get',
			dataType: "json",
			success: function (response)
			{
				$('#courseschedule-programrate').val(response.rate);
				$('#rate-per-month').val(response.ratePerMonth);
				$('#discounted-rate-per-month').val(response.ratePerMonthWithDiscount);
			}
		});
	}
};
$(document).ready(function () {
	$(document).on('change', '#course-programid, #courseschedule-duration, #courseschedule-programrate, #payment-frequency-discount, #enrolment-discount', function(){
		if ($(this).attr('id') != "course-programid") {
			var programRate = $('#courseschedule-programrate').val();
		} else {
			var programRate = null;
		}
		var duration = $('#courseschedule-duration').val();
		var programId = $('#course-programid').val();
		var paymentFrequencyDiscount = $('#payment-frequency-discount').val();
		var multiEnrolmentDiscount = $('#enrolment-discount').val();
		enrolment.fetchProgram(duration, programId, paymentFrequencyDiscount, multiEnrolmentDiscount, programRate);
        });
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
