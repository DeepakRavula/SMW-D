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