<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\grid\EnumColumn;
use common\models\User;
use common\models\TeacherAvailability;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->getPublicIdentity();
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">
		<div class="row-fluid">
		<p class="users-name">
			<?php echo !empty($model->userProfile->firstname) ? $model->userProfile->firstname : null ?>
			<?php echo !empty($model->userProfile->lastname) ? $model->userProfile->lastname : null ?></div>
		</div>
		<div class="row-fluid">
			<?php echo !empty($model->primaryAddress->address) ? $model->primaryAddress->address : null ?>
		</div>
		<div class="row-fluid">
			<?php echo !empty($model->phoneNumber->number) ? $model->phoneNumber->number : null ?>
		</div>
<!-- 	<?php
	// echo DetailView::widget([
	// 	'model' => $model,
	// 	'attributes' => [
	// 		[
	// 			'label' => 'First Name',
	// 			'value' => !empty($model->userProfile->firstname) ? $model->userProfile->firstname : null,
	// 		],
	// 		[
	// 			'label' => 'Last Name',
	// 			'value' => !empty($model->userProfile->lastname) ? $model->userProfile->lastname : null,
	// 		],
	// 		'email:email',
	// 		[
	// 			'label' => 'Address',
	// 			'value' => !empty($model->primaryAddress->address) ? $model->primaryAddress->address : null,
	// 		],
	// 		[
	// 			'label' => 'Phone Number',
	// 			'value' => !empty($model->phoneNumber->number) ? $model->phoneNumber->number : null,
	// 		],
	// 	],
	//])
	?> -->
	<p class="m-t-20">
		<?php echo Html::a(Yii::t('backend', '<i class="fa fa-pencil"></i> Edit details'), ['update', 'id' => $model->id], ['class' => 'm-r-20']) ?>
		<?php
		echo Html::a(Yii::t('backend', '<i class="fa fa-remove"></i> Delete'), ['delete', 'id' => $model->id], [
			'class' => '',
			'data' => [
				'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		])
		?>
    </p>
<hr>

<?php $roles = Yii::$app->authManager->getRolesByUser($model->id); $role = end($roles);?>
<?php if ( ! empty($role) && $role->name === User::ROLE_CUSTOMER): ?>
	<div class="col-md-12">
		<div class="row-fluid">
<h3 class="m-0 pull-left">Students </h3> 
<?php echo Html::a('<i class="fa fa-plus-circle"></i> Add new student', ['student/create'], ['class' => 'm-t-0 m-l-20 add-new-program text-add-new'])?>
<div class="clearfix"></div>
</div>
	<?php
	echo GridView::widget([
		'dataProvider' => $dataProvider,
		'options' => ['class'=>'m-t-10'],
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'label' => 'Name',
				'value' => function($data) {
					return !empty($data->fullName) ? $data->fullName : null;
				},
			],
			'birth_date',
			[
				'label' => 'Customer Name',
				'value' => function($data) {
					$fullName = !(empty($data->customer->userProfile->fullName)) ? $data->customer->userProfile->fullName : null;
					return $fullName;
				}
			],
			['class' => 'yii\grid\ActionColumn', 'controller' => 'student'],
		],
	]);
	?>
	<div class="clearfix"></div>
	</div>
	</div>

<?php endif; ?>
<?php if ( ! empty($role) && $role->name === User::ROLE_TEACHER): ?>
<div class="col-md-12">
<h4 class="pull-left m-r-20">Teachers Availability</h4>
<a href="#" class="availability text-add-new"><i class="fa fa-plus-circle"></i> Add availability</a>
<div class="clearfix"></div>
</div>
<div class="teacher-availability-create row-fluid">

    <?php echo $this->render('//teacher-availability/_form', [
        'model' => $teacherAvailabilityModel,
    ]) ?>

</div>
<?php
	echo GridView::widget([
		'dataProvider' => $dataProvider1,
		'options' => ['class' => 'col-md-5'],
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
                'label' => 'Day',
                'value' => function($data) {
                    if(! empty($data->day)){
                    $dayList = TeacherAvailability::getWeekdaysList();
                    $day = $dayList[$data->day];
                    return ! empty($day) ? $day : null;
                    }
                    return null;
                },
            ],
			[
                'label' => 'From Time',
                'value' => function($data) {
                    if(! empty($data->from_time)){
                    $fromTime = date("g:i a",strtotime($data->from_time));
                    return ! empty($fromTime) ? $fromTime : null;
                    }
                    return null;
                },
            ],
			[
                'label' => 'To Time',
                'value' => function($data) {
                    if(! empty($data->to_time)){
                    $toTime = date("g:i a",strtotime($data->to_time));
                    return ! empty($toTime) ? $toTime : null;
                    }
                    return null;
                },
            ],
			['class' => 'yii\grid\ActionColumn', 'controller' => 'teacher-availability','template' => '{delete}'],
		],
	]);
	?>
	<div class="clearfix"></div>
<?php endif; ?>

<script>
	$('.availability').click(function(){
		$('.teacher-availability-create').show(); 
	});
</script>
