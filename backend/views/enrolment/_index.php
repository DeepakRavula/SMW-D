<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use common\models\Course;
use common\models\CourseSchedule;
use common\models\discount\EnrolmentDiscount;
use common\models\UserProfile;
use common\models\UserAddress;
use common\models\UserPhone;
use common\models\UserContact;
use common\models\User;
use common\models\UserEmail;
use common\models\Student;
?>
<div id="error-notification" style="display: none;" class="alert-danger alert fade in"></div>
<?php $form = ActiveForm::begin([
	'id' => 'new-enrolment-form',
]); ?>
	    <div id="step-1">
	       <?= $this->render('new/_form-course', [
				'model' => new Course(), 
				'courseSchedule' => new CourseSchedule(),
				'paymentFrequencyDiscount' => new EnrolmentDiscount(),
				'multipleEnrolmentDiscount' => new EnrolmentDiscount(),
			   'form' => $form,
			]);?>
	    </div>
	    <div id="step-2">
	         <?=
			$this->render('new/_form-customer', [
				'model' => new User(),
				'userEmail' => new UserEmail(),
				'phoneModel' => new UserPhone(),
				'addressModel' => new UserAddress(),
				'userProfile' => new UserProfile(),
				'userContact' => new UserContact(),
				'form' => $form,
			]);
			?> 
	    </div>
	    <div id="step-3">
	     <?=
		$this->render('new/_form-student', [
			'model' => new Student(),
			'form' => $form,
		]);
		?> 
	    </div>

<?php ActiveForm::end(); ?>
<script>
$(document).ready(function () {
	$(document).on('click', '.step1-next', function() {
		$('#step-1, #step-3').hide();
		$('#step-2').show();
		return false;
	});
	$(document).on('click', '.step2-next', function() {
		$('#step-1, #step-2').hide();
		$('#step-3').show();
		var lastName = $('#userprofile-lastname').val();
		$('#student-last_name').val(lastName);
		return false;
	});
	$(document).on('click', '.step2-back', function() {
		$('#step-3, #step-2').hide();
		$('#step-1').show();
		return false;
	});
	$(document).on('click', '.step3-back', function() {
		$('#step-2, #step-3').hide();
		$('#step-2').show();
		return false;
	});
	$('#new-enrolment-form').on('afterValidate', function (event, messages) {
		if(messages["course-programid"].length || messages["userprofile-lastname"].length || messages["userprofile-firstname"].length || messages["userphone-number"].length || messages["useraddress-address"].length) {
			$('#notification').remove();
			$('#error-notification').html('Form has error. Please fix and try again.').fadeIn().delay(3000).fadeOut();
		}  else{
		}
    });
});
</script>
