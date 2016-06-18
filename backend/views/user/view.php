<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\grid\EnumColumn;
use common\models\User;
use common\models\TeacherAvailability;
use common\models\Address;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$roleNames = ArrayHelper::getColumn(
         	Yii::$app->authManager->getRoles(),'description'
        );
foreach($roleNames as $name => $description){
	if($name === $searchModel->role_name){
		$roleName = $description;
	}
}

$this->title = Yii::t('backend',  !($roleName) ? 'User' : $roleName.' Detail');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', ! $roleName ? 'User' : $roleName. 's'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
echo $this->render('_profile', [
        'model' => $model,
		'dataProvider1' => $dataProvider1,
		'teacherAvailabilityModel' => $teacherAvailabilityModel,
]);
 ?>
<div class="tabbable-panel">
	<div class="tabbable-line">
		
<?php $roles = Yii::$app->authManager->getRolesByUser($model->id); $role = end($roles);?>

<?php 

$studentContent = null;

if ( ! empty($role) && $role->name === User::ROLE_CUSTOMER)	 {
	$studentContent =  $this->render('_student', [
		'model'	=> $model,
		'dataProvider' => $dataProvider,
		'student' => $student,
	]);
}

$addressContent = $this->render('_contact',[
		'model'	=> $model,
		'dataProvider1' => $dataProvider1,
]);

// $phoneContent = $this->render('_phone',[
// 		'model'	=> $model,
// 		'dataProvider1' => $dataProvider1,
// ]);

$lessonContent = $this->render('_lesson',[
		'model'	=> $model,
		'lessonDataProvider' => $lessonDataProvider,
]);
?>
<?php echo Tabs::widget([
    'items' => [
		[
            'label' => 'Contact',
            'content' => $addressContent,
            // 'items' => [
            //      [
            //          'label' => 'Address',
            //          'content' => $addressContent,
            //      ],
            //      [
            //          'label' => 'Phone Number',
            //         'content' =>'coming soon..' ,
            //      ],
            //],
        ],
        [
            'label' => 'Students',
            'content' => $studentContent,
			'active' => true,
        ],
		[
			'label' => 'Lessons',
			'content' => $lessonContent,
		],
		[
			'label' => 'Invoices',
			'content' => 'coming soon..',
		],
	],
]);?>
<div class="clearfix"></div>
     </div>
 </div>
<script>
	$('.availability').click(function(){
		$('.teacher-availability-create').show(); 
	});
	$('.add-new-student').click(function(){
		$('.show-create-student-form').show();
	});
	
</script>
