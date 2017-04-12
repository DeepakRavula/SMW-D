<?php
use common\models\Course;
use kartik\editable\Editable;
use common\models\Enrolment;
use common\models\PaymentFrequency;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
?>
<div class="group-course-view">
	<div class="row-fluid user-details-wrapper">
    <div class="col-md-1 p-0" data-toggle="tooltip" data-placement="bottom" title="Program Name">
        	<i class="fa fa-music"></i> <?= $model->course->program->name; ?>
    </div>
	<div class="col-md-2" data-toggle="tooltip" data-placement="bottom" title="Teacher Name">
        	<i class="fa fa-graduation-cap"></i> <?= $model->course->teacher->publicIdentity; ?>
    </div>
    <div class="col-md-1" data-toggle="tooltip" data-placement="bottom" title="Rate">
    	<i class="fa fa-money"></i> <?= $model->course->program->rate; ?>
    </div>
	<div class="col-md-1" data-toggle="tooltip" data-placement="bottom" title="Duration">
    	<i class="fa fa-calendar"></i> <?php 
        $length = \DateTime::createFromFormat('H:i:s', $model->course->duration);
        echo $length->format('H:i'); ?>
    </div>
	<div class="col-md-1" data-toggle="tooltip" data-placement="bottom" title="Day">
    	<i class="fa fa-calendar"></i> <?php
        $dayList = Course::getWeekdaysList();
        $day = $dayList[$model->course->day];
        echo $day; ?>
    </div>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Time">
		<i class="fa fa-clock-o"></i> <?php 
        $fromTime = \DateTime::createFromFormat('H:i:s', $model->course->fromTime);
        echo $fromTime->format('h:i A'); ?>	
	</div>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Start Date">
			<i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDate($model->course->startDate)?>	
	</div>
		<div class="row-fluid">
	<div class="col-md-1 p-0 hand" data-toggle="tooltip" data-placement="bottom" title="End Date">
			<i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDate($model->course->endDate)?>	
	</div>
    <div class="clearfix"></div>
	<?php if($model->course->program->isPrivate()) : ?>
	<div class="col-md-1 p-0 hand" data-toggle="tooltip" data-placement="bottom" title="Payment Frequency">
		<?=
		 Editable::widget([
			'name'=>'paymentFrequencyId',
			'asPopover' => true,
			'inputType' => Editable::INPUT_DROPDOWN_LIST,
			'value' => $model->getPaymentFrequency(),
			'header' => 'Payment Frequency',
			'submitOnEnter' => false,
			'size'=>'md',
			'data' => ArrayHelper::map(PaymentFrequency::find()->all(), 'id', 'name'),
    		'formOptions' => ['action' => Url::to(['/enrolment/edit', 'id' => $model->id])],
		])
		?> 
	</div>
	<?php endif; ?>
    <div class="clearfix"></div>
</div>
</div>