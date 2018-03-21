<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use backend\models\discount\PaymentFrequencyEnrolmentDiscount;
use common\models\Course;
use common\models\CourseSchedule;
use backend\models\discount\MultiEnrolmentDiscount;

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
            'paymentFrequencyDiscount' => new PaymentFrequencyEnrolmentDiscount(),
            'multipleEnrolmentDiscount' => new MultiEnrolmentDiscount(),
            'student' => $student,
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
	fetchProgram: function(duration, programId, paymentFrequencyDiscount, multiEnrolmentDiscount, programRate, customerDiscount) {
		var params = $.param({duration: duration, id: programId, paymentFrequencyDiscount: paymentFrequencyDiscount,
			multiEnrolmentDiscount: multiEnrolmentDiscount, rate: programRate, customerDiscount : customerDiscount });
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
		if($('#course-teacherid').val() == "") {
			$('#enrolment-form').yiiActiveForm('updateAttribute', 'course-teacherid', ["Teacher cannot be blank"]);
			
		}else if($('#courseschedule-day').val() == "") {
			$('#error-notification').html('Please choose the date/time in the calendar').fadeIn().delay(3000).fadeOut();
		}
		$('#notification').remove();
		$('.field-courseschedule-fromtime p').text('');
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
	$(document).on('change', '#course-programid, #courseschedule-duration, #courseschedule-programrate, #paymentfrequencyenrolmentdiscount-discount, #multienrolmentdiscount-discount', function(){
		if ($(this).attr('id') != "course-programid") {
			var programRate = $('#courseschedule-programrate').val();
		} else {
			var programRate = null;
		}
		var duration = $('#courseschedule-duration').val();
		var programId = $('#course-programid').val();
		var paymentFrequencyDiscount = $('#paymentfrequencyenrolmentdiscount-discount').val();
		var multiEnrolmentDiscount = $('#multienrolmentdiscount-discount').val();
		var customerDiscount = $('#customer-discount').val();
		enrolment.fetchProgram(duration, programId, paymentFrequencyDiscount, multiEnrolmentDiscount, programRate, customerDiscount);
        });
});
</script>
