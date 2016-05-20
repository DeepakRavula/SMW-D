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



	<?php
	echo DetailView::widget([
		'model' => $model,
		'attributes' => [
			[
				'label' => 'First Name',
				'value' => !empty($model->userProfile->firstname) ? $model->userProfile->firstname : null,
			],
			[
				'label' => 'Last Name',
				'value' => !empty($model->userProfile->lastname) ? $model->userProfile->lastname : null,
			],
			'email:email',
			[
				'label' => 'Address',
				'value' => !empty($model->primaryAddress->address) ? $model->primaryAddress->address : null,
			],
			[
				'label' => 'Phone Number',
				'value' => !empty($model->phoneNumber->number) ? $model->phoneNumber->number : null,
			],
		],
	])
	?>
	<p>
		<?php echo Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?php
		echo Html::a(Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		])
		?>
    </p>

</div>
<?php $roles = Yii::$app->authManager->getRolesByUser($model->id); $role = end($roles);?>
<?php if ( ! empty($role) && $role->name === User::ROLE_CUSTOMER): ?>
<h3>Students </h3> 
<?php echo Html::a('Create Student', ['student/create'], ['class' => 'btn btn-success'])?>

	<?php
	echo GridView::widget([
		'dataProvider' => $dataProvider,
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
<?php endif; ?>
<?php if ( ! empty($role) && $role->name === User::ROLE_TEACHER): ?>
<h4>Teachers Availability</h4>
<?php
	echo GridView::widget([
		'dataProvider' => $dataProvider1,
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
			['class' => 'yii\grid\ActionColumn', 'controller' => 'teacher-availability'],
		],
	]);
	?>
<div class="teacher-availability-create">

    <?php echo $this->render('//teacher-availability/_form', [
        'model' => $teacherAvailabilityModel,
    ]) ?>

</div>
<?php endif; ?>
