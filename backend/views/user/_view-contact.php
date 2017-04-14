<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use common\models\User;
use kartik\editable\Editable;
use yii\helpers\Url;
?>
<div class="row p-10">
	<div class="col-xs-4">
		<div class="row-fluid">
		<p class="c-title m-0"><i class="fa fa-map-marker"></i> Addresses </p>
		  <?php echo ListView::widget([
                'dataProvider' => $addressDataProvider,
                'itemView' => '_view-contact-address',
            ]); ?>
		</div>
	</div>
	<div class="col-xs-4">
		<div class="row-fluid">
			<p class="c-title m-0"><i class="fa fa-phone-square"></i> Phone Numbers</p>
			<?php echo ListView::widget([
                'dataProvider' => $phoneDataProvider,
                'itemView' => '_view-contact-phone',
            ]); ?>
		</div>
		<hr>
		<div class="row-fluid m-t-10 m-b-20">
			<div class="col-xs-2 p-0 c-title"><i class="fa fa-envelope"></i>  Email</p></div>
			<div class="col-xs-3"><?php echo!empty($model->email) ? $model->email : null ?></div>
			<div class="clearfix"></div>
		</div>
	</div>
	<?php if($searchModel->role_name === User::ROLE_TEACHER) : ?>
	<?php $privateLessonRate = !empty($model->teacherPrivateLessonRate->hourlyRate) ? $model->teacherPrivateLessonRate->hourlyRate : null; 
	$groupLessonRate = !empty($model->teacherGroupLessonRate->hourlyRate) ? $model->teacherGroupLessonRate->hourlyRate : null;
	?> 
	<div class="col-xs-4">
		<p class="c-title m-0"> Hourly Rate ($)</p>
		<div class="row-fluid m-t-10 m-b-20">
			<div class="col-xs-4 p-0 c-title"> Private Lesson</div>
			<div class="col-xs-4">
				<?=
				 Editable::widget([
					'name'=>'privateLessonHourlyRate',
					'asPopover' => true,
					'value' => $privateLessonRate,
					'inputType' => Editable::INPUT_TEXT,
					'header' => 'Private Lesson Hourly Rate',
                    'placement' => 'bottom',
					'submitOnEnter' => true,
					'size'=>'md',
					'formOptions' => ['action' => Url::to(['teacher-rate/update', 'id' => $model->id])],
				])
				?> 
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row-fluid m-t-10 m-b-20">
			<div class="col-xs-4 p-0 c-title"> Group Lesson</div>
			<div class="col-xs-4">
				<?=
				 Editable::widget([
					'name'=>'groupLessonHourlyRate',
					'asPopover' => true,
					'value' => $groupLessonRate,
					'inputType' => Editable::INPUT_TEXT,
                    'placement' => 'bottom',
					'header' => 'Group Lesson Hourly Rate',
					'submitOnEnter' => true,
					'size'=>'md',
					'formOptions' => ['action' => Url::to(['teacher-rate/update', 'id' => $model->id])],
				])
				?> 
			</div>
			<div class="clearfix"></div>
		</div>	
			
	</div>
	<?php endif; ?>
	<div class="clearfix"></div>
	<div class="col-xs-12">
		<?php echo Html::a('<i class="fa fa-pencil"></i> Edit Contact Information', ['update', 'UserSearch[role_name]' => $searchModel->role_name, 'id' => $model->id, '#' => 'contact'], ['class' => 'm-R-20']) ?>
	</div>
</div>
