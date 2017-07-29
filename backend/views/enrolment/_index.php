<?php
use yii\helpers\Url;
use common\models\Course;
use common\models\UserProfile;
use common\models\Address;
use common\models\PhoneNumber;
use common\models\User;
use common\models\Student;
use yii\bootstrap\Modal;
use yii\bootstrap\ActiveForm;
use common\models\CourseSchedule;
use common\models\EnrolmentDiscount;
use kartik\datetime\DateTimePickerAsset;
DateTimePickerAsset::register($this);
?>
<link type="text/css" href="/plugins/bootstrap-datepicker/bootstrap-datepicker.css" rel='stylesheet' />
<script type="text/javascript" src="/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>

<?php $form = ActiveForm::begin([
	'id' => 'new-enrolment-form',
	'action' => Url::to(['enrolment/add'])
]); ?>
<div class="container-fluid">
	<div class="row">
		<div class="p-15">
			<div id="accordion" class="enrolment">
				<div class="panel enrolment-step">
					<div class="panel-heading"> <span class="enrolment-step-number">1</span>
						<h4 class="enrolment-step-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" > Program</a></h4>
					</div>
					<div id="collapseOne" class="collapse in">
						<div class="enrolment-step-body">
							<?=
							$this->render('new/_form-course', [
								'model' => new Course(),
								'courseSchedule' => new CourseSchedule(),
								'paymentFrequencyDiscount' => new EnrolmentDiscount(),
								'multipleEnrolmentDiscount' => new EnrolmentDiscount(),
								'form' => $form,
							]);
							?>
							<div class="row">
							<div class="col-md-12">
								<a class="collapsed btn btn-default pull-left m-r-10" href="<?= Url::to(['enrolment/index', 'EnrolmentSearch[showAllEnrolments]' => false]);?>"> Cancel<a>
								<a class="collapsed btn btn-primary" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" id="step1-btn"> Next </a>
								<div class="clearfix"></div>
							</div>
							<div class="clearfix"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel enrolment-step">
					<div class="panel-heading" role="tab" id="headingTwo"> <span class="enrolment-step-number">2</span>
						<h4 class="enrolment-step-title"> <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" > Customer </a> </h4>
					</div>
					<div id="collapseTwo" class="panel-collapse collapse">
						<div class="enrolment-step-body">
							<div class="row">
								<?=
								$this->render('new/_form-customer', [
									'model' => new User(),
									'phoneModel' => new PhoneNumber(),
									'addressModel' => new Address(),
									'userProfile' => new UserProfile(),
									'form' => $form,
								]);
								?> 
							</div>
							<a class="collapsed btn btn-default pull-left" href="<?= Url::to(['enrolment/index', 'EnrolmentSearch[showAllEnrolments]' => false]);?>"> Cancel<a>
							<a class="collapsed btn btn-default m-l-10 m-r-10" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne"> Back </a>
							<a class="collapsed btn btn-primary" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" id="step2-btn"> Next </a>
						</div>
					</div>
				</div>
				<div class="panel enrolment-step">
					<div class="panel-heading" role="tab" id="headingThree"> <span class="enrolment-step-number">3</span>
						<h4 class="enrolment-step-title"> <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree"  > Student </a> </h4>
					</div>
					<div id="collapseThree" class="panel-collapse collapse">
						<div class="enrolment-step-body">
							<div class="row">
								<div class="form-group">
									<?=
									$this->render('new/_form-student', [
										'model' => new Student(),
										'form' => $form,
									]);
									?> 
								</div>
							</div>
							<a class="collapsed btn btn-default pull-left" href="<?= Url::to(['enrolment/index', 'EnrolmentSearch[showAllEnrolments]' => false]);?>"> Cancel<a>
							<a class="collapsed btn btn-default m-l-10 m-r-10" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"> Back </a>
							<a class="collapsed btn btn-primary" role="button" data-toggle="collapse" id="step3-btn" data-parent="#accordion" href=""> Preview Lessons </a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php ActiveForm::end(); ?>
<?php
Modal::begin([
	'header' => '<h4 class="m-0">Choose Teacher, Day and Time</h4>',
	'id' => 'new-enrolment-modal',
]);
?>
<?php
echo $this->render('new/_calendar', [
	'model' => new course(),
	'courseSchedule' => new CourseSchedule()
]);
?>
<div id="enrolment-calendar"></div>
<?php Modal::end(); ?>
<script type="text/javascript">
	$(document).ready(function(){
		$('#step1-btn').click(function() {
			var day = $('#courseschedule-day').val();
			if(! day) {
				$('#notification').html('Please check the schedule in step one').fadeIn().delay(4000).fadeOut();
			}
        });
		$('#step3-btn').click(function() {
			$('#new-enrolment-form').data('yiiActiveForm').submitting = true;
			$('#new-enrolment-form').yiiActiveForm('validate');
        });
		$('#new-enrolment-form').on('afterValidate', function (event, messages) {
			if(messages["course-programid"].length || 
				messages["course-teacherid"].length ||
				messages["userprofile-firstname"].length ||
				messages["userprofile-lastname"].length || messages["address-address"].length || messages["phonenumber-number"].length){ 
			} else {
				$.ajax({
                url: $('#new-enrolment-form').attr('action'),
                type: 'post',
                dataType: "json",
                data: $('#new-enrolment-form').serialize(),
                success: function (response)
                {
                }
            });	
			return false;
			}
        });
	});
</script>