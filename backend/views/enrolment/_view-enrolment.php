<?php
use yii\data\ActiveDataProvider;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use common\models\Course;
use common\models\Enrolment;
use yii\helpers\Url;

use kartik\datetime\DateTimePickerAsset;
DateTimePickerAsset::register($this);	
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
        $length = \DateTime::createFromFormat('H:i:s', $model->courseSchedule->duration);
        echo $length->format('H:i'); ?>
    </div>
	<div class="col-md-1" data-toggle="tooltip" data-placement="bottom" title="Day">
    	<i class="fa fa-calendar"></i> <?php
        $dayList = Course::getWeekdaysList();
        $day = $dayList[$model->courseSchedule->day];
        echo $day; ?>
    </div>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Time">
		<i class="fa fa-clock-o"></i> <?php
        $fromTime = \DateTime::createFromFormat('H:i:s', $model->courseSchedule->fromTime);
        echo $fromTime->format('h:i A'); ?>
	</div>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Start Date">
			<i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDate($model->course->startDate)?>
	</div>
		<div class="row-fluid">
	<?php yii\widgets\Pjax::begin(['id' => 'course-enddate','timeout' => 6000,]); ?>
	<div class="col-md-1 p-0 hand" data-toggle="tooltip" data-placement="bottom" title="End Date">
			<i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDate($model->course->endDate)?>
	</div>
	<?php yii\widgets\Pjax::end(); ?>
    <div class="clearfix"></div>
	<?php if($model->course->program->isPrivate()) :
            $enrolmentDataProvider = new ActiveDataProvider([
            'query' => Enrolment::find()
                ->where(['id' => $model->id]),
        ]);?>
    <?php yii\widgets\Pjax::begin(['id' => 'enrolment-view']); ?>
	<?php echo GridView::widget([
        'dataProvider' => $enrolmentDataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => '',
        'columns' => [
            [
                'label' => 'Payment Frequency',
                'value' => function($data) {
                    return $data->getPaymentFrequency();
                }
            ],
            [
                'label' => 'Payment Frequency Discount',
                'value' => function($data) {
                    return $data->getPaymentFrequencyDiscountValue();
                }
            ],
            [
                'label' => 'Multiple Enrolment Discount',
                'value' => function($data) {
                    return $data->getMultipleEnrolmentDiscountValue();
                }

            ],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{update}'
            ],
        ],
    ]); ?>
    <?php yii\widgets\Pjax::end(); ?>
	<?php endif; ?>
    <div class="clearfix"></div>
</div>
</div>
</div>
    <?php Modal::begin([
        'header' => '<h4 class="m-0">Enrolment Edit</h4>',
        'id' => 'enrolment-edit-modal',
    ]); ?>
    <div id="enrolment-edit-content"></div>
    <?php Modal::end(); ?>

<script>
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    
    $(document).on('click', '.enrolment-edit-cancel', function(){
        $('#enrolment-edit-modal').modal('hide');
        return false;
    });

    $(document).on('click', '.glyphicon-pencil', function(){
        $.ajax({
            url    : '<?= Url::to(['enrolment/edit', 'id' => $model->id]); ?>',
            type   : 'get',
            dataType: "json",
            success: function(response)
            {
                if(response.status)
                {
                    $('#enrolment-edit-content').html(response.data);
                    $('#enrolment-edit-modal').modal('show');
                    $('#warning-notification').html('You have entered a \n\
                    non-approved Arcadia discount. All non-approved discounts \n\
                    must be submitted in writing and approved by Head Office \n\
                    prior to entering a discount, otherwise you are in breach \n\
                    of your agreement.').fadeIn();
                }
            }
        });
        return false;
    });

    $(document).on('beforeSubmit', '#enrolment-update-form', function(){
        $.ajax({
            url    : '<?= Url::to(['enrolment/edit', 'id' => $model->id]); ?>',
            type   : 'post',
            dataType: "json",
            data: $(this).serialize(),
            success: function(response)
            {
                if(response.status)
                {
                    $('#enrolment-edit-modal').modal('hide');
                    paymentFrequency.onEditableSuccess();
                }
            }
        });
        return false;
    });
    
    var paymentFrequency = {
	onEditableSuccess :function(event, val, form, data) {
            var url = "<?php echo Url::to(['enrolment/view', 'id' => $model->id]); ?>"
            $.pjax.reload({url:url,container:"#payment-cycle-listing",replace:false, async:false, timeout: 4000});
            $.pjax.reload({url:url,container:"#enrolment-view",replace:false, async:false, timeout: 4000});
            $.pjax.reload({url:url,container:"#course-enddate",replace:false, async:false, timeout: 4000});
        }
    }
</script>