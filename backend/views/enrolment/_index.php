<?php
use yii\helpers\Url;
use common\models\Course;
use common\models\UserProfile;
use common\models\UserAddress;
use common\models\UserPhone;
use common\models\UserContact;
use common\models\User;
use common\models\Student;
use yii\bootstrap\ActiveForm;
use common\models\UserEmail;
use common\models\CourseSchedule;
use common\models\discount\EnrolmentDiscount;
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
	<div class="row">
          <div class="box box-default">
            <!-- /.box-header -->
            <div class="box-body">
              <div class="box-group" id="accordion">
                <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                <div class="panel box box-primary">
                  <div class="box-header with-border">
                    <h4 class="box-title">
                      <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" class="">
                        Program
                      </a>
                    </h4>
                  </div>
                  <div id="collapseOne" class="panel-collapse collapse in" aria-expanded="true" style="">
                    <div class="box-body">
                      <?=
						$this->render('new/_form-course', [
							'model' => new Course(),
							'courseSchedule' => new CourseSchedule(),
							'paymentFrequencyDiscount' => new EnrolmentDiscount(),
							'multipleEnrolmentDiscount' => new EnrolmentDiscount(),
							'form' => $form,
						]);
						?>
                        <div class="pull-right">
						<a class="collapsed btn btn-default pull-left m-r-10" href="<?= Url::to(['enrolment/index', 'EnrolmentSearch[showAllEnrolments]' => false]);?>"> Cancel<a>
						<a class="collapsed btn btn-primary" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" id="step1-btn"> Next </a>
                        </div>
                        </div>
                  </div>
                </div>
                <div class="panel box box-primary">
                  <div class="box-header with-border">
                    <h4 class="box-title">
                      <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" class="collapsed" aria-expanded="false">
						  Customer
                      </a>
                    </h4>
                  </div>
                  <div id="collapseTwo" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                    <div class="box-body">
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
					<a class="collapsed btn btn-default m-r-10" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne"> Back </a>
					<a class="collapsed btn btn-primary m-r-10" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" id="step2-btn"> Next </a>
					<a class="collapsed btn btn-default" href="<?= Url::to(['enrolment/index', 'EnrolmentSearch[showAllEnrolments]' => false]);?>"> Cancel<a>
                    </div>
                  </div>
                </div>
                <div class="panel box box-primary">
                  <div class="box-header with-border">
                    <h4 class="box-title">
                      <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree" class="collapsed" aria-expanded="false">
                        Student
                      </a>
                    </h4>
                  </div>
                  <div id="collapseThree" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                    <div class="box-body">
                     <?=
						$this->render('new/_form-student', [
							'model' => new Student(),
							'form' => $form,
						]);
						?> 
					<a class="collapsed btn btn-default m-r-10" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"> Back </a>
					<a class="collapsed btn btn-primary m-r-10" role="button" data-toggle="collapse" id="step3-btn" data-parent="#accordion" href=""> Preview Lessons </a>
					<a class="collapsed btn btn-default pull-left m-r-10" href="<?= Url::to(['enrolment/index', 'EnrolmentSearch[showAllEnrolments]' => false]);?>"> Cancel<a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>	
<?php ActiveForm::end(); ?>
<?php
echo $this->render('new/_calendar', [
	'model' => new course(),
	'courseSchedule' => new CourseSchedule()
]);
?>
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
				messages["courseschedule-day"].length ||
				messages["course-teacherid"].length ||
				messages["userprofile-firstname"].length ||
				messages["userprofile-lastname"].length || messages["useraddress-address"].length || messages["userphone-number"].length){ 
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