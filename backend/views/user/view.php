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

<div class="user-view user-details-wrapper">
		<div class="col-md-12 users-name">
			<p class="users-name"><?php echo !empty($model->userProfile->firstname) ? $model->userProfile->firstname : null ?>
				<?php echo !empty($model->userProfile->lastname) ? $model->userProfile->lastname : null ?> 
				<em>
					<small><?php echo !empty($model->email) ? $model->email : null ?></small>
				</em>
			</p>
		</div>
		<div class="col-md-2">
			<i class="fa fa-map-marker"></i> <?php echo !empty($address->address) ? $address->address : null ?>
		</div>
		<div class="col-md-2">
			<i class="fa fa-phone-square"></i> <?php echo !empty($model->phoneNumber->number) ? $model->phoneNumber->number : null ?>
		</div>
		<div class="clearfix"></div>
<?php $roles = Yii::$app->authManager->getRolesByUser($model->id); $role = end($roles);?>
<?php if ( ! empty($role) && $role->name === User::ROLE_TEACHER): ?>
<div class="row-fluid m-t-20">
    <div class="col-md-2">
        <h5 class="m-t-5"><i class="fa fa-graduation-cap"></i> Qualifications</h5>
    </div>
    <div class="col-md-10">
       <span class="label label-primary"><?= $program?></span>
    </div>
    <div class="clearfix"></div>
</div>
<?php endif; ?>
	<div class="col-md-12 action-btns">
		<?php echo Html::a(Yii::t('backend', '<i class="fa fa-pencil"></i> Update details'), ['update', 'id' => $model->id], ['class' => 'm-r-20']) ?>
		<?php
		echo Html::a(Yii::t('backend', '<i class="fa fa-remove"></i> Delete'), ['delete', 'id' => $model->id], [
			'class' => '',
			'data' => [
				'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		])
		?>
    </div>
    <div class="clearfix"></div>
</div>
</div>
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
	?>
	<div class="clearfix"></div>
	

<?php if ( ! empty($role) && $role->name === User::ROLE_TEACHER): ?>
<!-- <div class="col-md-12">
    <div class="col-md-2">
        <h4>Qualifications</h4>
    </div>
    <div class="col-md-10">
       <h4> <?php //echo $program?></h4>
    </div>
    <div class="clearfix"></div>
</div> -->
<div class="col-md-12">
<h4 class="pull-left m-r-20">Teachers Availability</h4>
<a href="#" class="availability text-add-new"><i class="fa fa-plus-circle"></i> Add availability</a>
<div class="clearfix"></div>
</div>
<div class="teacher-availability-create row-fluid">
<?php
$profileContent = $this->render('_profile',[
		'model'	=> $model,
		'dataProvider1' => $dataProvider1,
		'teacherAvailabilityModel' => $teacherAvailabilityModel,
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
