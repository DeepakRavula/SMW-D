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
//	'action' => Url::to(['enrolment/add']),
]); ?>
	    <div class="row setup-content" id="step-1">
	       <?= $this->render('new/_form', [
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
