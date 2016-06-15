<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\grid\EnumColumn;
use common\models\User;
use common\models\TeacherAvailability;
use common\models\Address;
use yii\helpers\ArrayHelper;

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
<?php //echo '<pre>'; print_r($addressModels) ?>
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
		<div class="col-md-12">
			<h4 class="pull-left m-r-20">Students </h4> 
			<a href="#" class="add-new-student text-add-new"><i class="fa fa-plus-circle"></i> Add new student</a>
			<?php //echo Html::a('<i class="fa fa-plus-circle"></i> Add new student', ['student/create'], ['class' => 'add-new-program text-add-new'])?>
			<div class="clearfix"></div>
		</div>
		<div class="dn show-create-student-form">
		    <?php echo $this->render('//student/create', [
		        'model' => $student,
				'customer' => $model,
		    ]) ?>
		</div>

	<?php
	echo GridView::widget([
		'dataProvider' => $dataProvider,
		'rowOptions' => function ($model, $key, $index, $grid) {
            $u= \yii\helpers\StringHelper::basename(get_class($model));
            $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
            return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
        },
		'options' => ['class'=>'col-md-12'],
		'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'label' => 'Name',
				'value' => function($data) {
					return !empty($data->fullName) ? $data->fullName : null;
				},
			],
			'birth_date:date',
			[
				'label' => 'Customer Name',
				'value' => function($data) {
					$fullName = !(empty($data->customer->userProfile->fullName)) ? $data->customer->userProfile->fullName : null;
					return $fullName;
				}
			],
			//['class' => 'yii\grid\ActionColumn', 'controller' => 'student'],
		],
	]);
	?>
	<div class="clearfix"></div>
	

<?php endif; ?>
<?php if ( ! empty($role) && $role->name === User::ROLE_TEACHER): ?>
<div class="col-md-12">
    <div class="col-md-2">
        <h4>Qualifications</h4>
    </div>
    <div class="col-md-10">
       <h4> <?= $program?></h4>
    </div>
    <div class="clearfix"></div>
</div>
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
		'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
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
	$('.add-new-student').click(function(){
		$('.show-create-student-form').show();
	});
	
</script>
