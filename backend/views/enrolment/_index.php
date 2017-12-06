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
use common\models\LocationAvailability;
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
   <?= $this->render('new/_form-teacher', [
		'model' => new Course(), 
		'courseSchedule' => new CourseSchedule(),
		'form' => $form
	]);?>
</div>
<div id="step-3">
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
<div id="step-4">
 <?=
$this->render('new/_form-student', [
	'model' => new Student(),
	'form' => $form,
]);
?> 
</div>
<?php ActiveForm::end(); ?>
<?php
    $locationId = Yii::$app->session->get('location_id');
    $minLocationAvailability = LocationAvailability::find()
        ->where(['locationId' => $locationId])
        ->orderBy(['fromTime' => SORT_ASC])
        ->one();
    $maxLocationAvailability = LocationAvailability::find()
        ->where(['locationId' => $locationId])
        ->orderBy(['toTime' => SORT_DESC])
        ->one();
    $from_time = (new \DateTime($minLocationAvailability->fromTime))->format('H:i:s');
    $to_time = (new \DateTime($maxLocationAvailability->toTime))->format('H:i:s');
?>
<script>
$(document).ready(function () {
	function loadCalendar() {
 		var date = $('#course-startdate').val();
        $('#reverse-enrolment-calendar').fullCalendar({
     		defaultDate: moment(date, 'DD-MM-YYYY', true).format('YYYY-MM-DD'),
            schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
             header: {
                 left: 'prev,next today',
                 center: 'title',
                 right: ''
             },
             allDaySlot: false,
             slotDuration: '00:15:00',
             titleFormat: 'DD-MMM-YYYY, dddd',
             defaultView: 'agendaWeek',
             minTime: "<?php echo $from_time; ?>",
             maxTime: "<?php echo $to_time; ?>",
             selectConstraint: 'businessHours',
             eventConstraint: 'businessHours',
             businessHours: [],
             allowCalEventOverlap: true,
             overlapEventsSeparate: true,
             events: [],
     	});
	}
	$(document).on('click', '.step1-next', function() {
		$('#step-1, #step-3, #step-4').hide();
		loadCalendar();
 		$('#reverse-enrol-modal .modal-dialog').css({'width': '1000px'});
		$('#step-2').show();
		return false;
	});
	$(document).on('click', '.step2-next', function() {
		$('#step-1, #step-2, #step-4').hide();
		$('#step-3').show();
 		$('#reverse-enrol-modal .modal-dialog').css({'width': '600px'});
		return false;
	});
	$(document).on('click', '.step2-back', function() {
		$('#step-3, #step-2, #step-4').hide();
		$('#step-1').show();
 		$('#reverse-enrol-modal .modal-dialog').css({'width': '600px'});
		return false;
	});
	$(document).on('click', '.step3-next', function() {
		$('#step-1, #step-2, #step-3').hide();
		$('#step-4').show();
 		$('#reverse-enrol-modal .modal-dialog').css({'width': '400px'});
		var lastName = $('#userprofile-lastname').val();
		$('#student-last_name').val(lastName);
		return false;
	});
	$(document).on('click', '.step3-back', function() {
		$('#step-3, #step-1, #step-4').hide();
		$('#step-2').show();
 		$('#reverse-enrol-modal .modal-dialog').css({'width': '1000px'});
		return false;
	});
	$(document).on('click', '.step4-back', function() {
		$('#step-2, #step-3, #step-4').hide();
		$('#step-3').show();
 		$('#reverse-enrol-modal .modal-dialog').css({'width': '600px'});
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
