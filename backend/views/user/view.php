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

$roles = ArrayHelper::getColumn(
         	Yii::$app->authManager->getRoles(),'description'
        );
foreach($roles as $name => $description){
	if($name === $searchModel->role_name){
		$role = $description;
	}
}

$this->title = Yii::t('backend',  !($role) ? 'User' : $role.' Detail');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', ! $role ? 'User' : $role. 's'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $roles = Yii::$app->authManager->getRolesByUser($model->id); $role = end($roles);?>
<?php if ( ! empty($role) && $role->name === User::ROLE_CUSTOMER): ?>
<?php endif; ?>
<?php //echo '<pre>'; print_r($addressModels) ?>
<div class="user-view user-details-wrapper">

<?php 

$studentContent = null;

if ( ! empty($role) && $role->name === User::ROLE_CUSTOMER)	 {
	$studentContent =  $this->render('_student', [
		'model'	=> $model,
		'dataProvider' => $dataProvider,
		'student' => $student,
	]);
}

$profileContent = $this->render('_profile',[
		'model'	=> $model,
		'dataProvider1' => $dataProvider1,
		'teacherAvailabilityModel' => $teacherAvailabilityModel,
]);

$addressContent = $this->render('_address',[
		'model'	=> $model,
		'dataProvider1' => $dataProvider1,
]);

$phoneContent = $this->render('_phone',[
		'model'	=> $model,
		'dataProvider1' => $dataProvider1,
]);

?>
<?php echo Tabs::widget([
    'items' => [
        [
            'label' => 'Profile',
            'content' => $profileContent,
            'active' => true
        ],
		[
            'label' => 'Contacts',
            'items' => [
                 [
                     'label' => 'Address',
                     'content' => 'address',
                 ],
                 [
                     'label' => 'Phone Number',
                    'content' =>'phone' ,
                 ],
            ],
        ],
        [
            'label' => 'Students',
            'content' => $studentContent,
        ],
    ],
]);?>

<script>
	$('.availability').click(function(){
		$('.teacher-availability-create').show(); 
	});
	$('.add-new-student').click(function(){
		$('.show-create-student-form').show();
	});
	
</script>
