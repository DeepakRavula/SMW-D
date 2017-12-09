<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use common\models\LocationAvailability;
use common\models\Course;
use common\models\CourseSchedule;
use backend\models\discount\EnrolmentDiscount;
?>
<div id="error-notification" style="display: none;" class="alert-danger alert fade in"></div>
<div class="user-create-form">

<?php
$form = ActiveForm::begin([
		'id' => 'enrolment-form',
		'action' => Url::to(['student/enrolment', 'id' => $student->id]),
	]);
?>	
	    <div id="step-1">
	    <?= $this->render('_step-one', [
			'model' => new Course(), 
			'courseSchedule' => new CourseSchedule(),
			'paymentFrequencyDiscount' => new EnrolmentDiscount(),
			'multipleEnrolmentDiscount' => new EnrolmentDiscount(),
			'student' => $model,
			'form' => $form
		]);?> 
	    </div>
	    <div id="step-2">
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
	$('#enrolment-form').on('afterValidate', function (event, messages) {
		if(messages["course-programid"].length) {
			$('#notification').remove();
			$('.field-courseschedule-fromtime p').text('');
			$('#error-notification').html('Form has error. Please fix and try again.').fadeIn().delay(3000).fadeOut();
		}  else{
		}
    });
	 $(document).on('beforeSubmit', '#enrolment-form', function(){
        $.ajax({
            url    : $(this).attr('action'),
            type   : 'post',
            dataType: "json",
            data: $(this).serialize(),
            success: function(response)
            {
            }
        });
        return false;
    });
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
});
</script>
