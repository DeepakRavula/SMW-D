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
			<div class="requestwizard">
				<div class="requestwizard-row setup-panel">
					<div class="requestwizard-step">
			            <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
			            <p>Program</p>
			        </div>
			        <div class="requestwizard-step">
			            <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
			            <p>Customer</p>
			        </div>
			        <div class="requestwizard-step">
			            <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
			            <p>Student</p>
			        </div>
		    	</div>
			</div>
<div class="clearfix"></div>	
<?php $form = ActiveForm::begin([
	'id' => 'new-enrolment-form',
]); ?>
	    <div class="row setup-content" id="step-1">
	       <?= $this->render('new/_form-course', [
				'model' => new Course(), 
				'courseSchedule' => new CourseSchedule(),
				'paymentFrequencyDiscount' => new EnrolmentDiscount(),
				'multipleEnrolmentDiscount' => new EnrolmentDiscount(),
			   'form' => $form,
			]);?>
	    </div>
	    <div class="row setup-content" id="step-2">
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
	    <div class="row setup-content" id="step-3">
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
	$('#new-enrolment-form').on('afterValidate', function (event, messages) {
		if(messages["course-programid"].length || messages["userprofile-lastname"].length || messages["userprofile-firstname"].length || messages["userphone-number"].length || messages["useraddress-address"].length) {
			$('#notification').remove();
			$('#error-notification').html('Form has error. Please fix and try again.').fadeIn().delay(3000).fadeOut();
		}  else{
		}
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
