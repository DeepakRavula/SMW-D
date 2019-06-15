<?php 

use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;

use kartik\datetime\DateTimePickerAsset;

DateTimePickerAsset::register($this);
?>
<div class="row-fluid">
	<a href="#" title="Add" class="add-unavailability pull-right"><i class="fa fa-plus"></i></a>
</div>
<div>
<?php yii\widgets\Pjax::begin([
    'id' => 'unavailability-list'
]) ?>
<?php
echo GridView::widget([
'id' => 'unavailability-grid',
'dataProvider' => $unavailabilityDataProvider,
'summary' => false,
'emptyText' => false,
'options' => ['class' => 'col-md-12'],
'tableOptions' => ['class' => 'table table-bordered m-t-15'],
'headerRowOptions' => ['class' => 'bg-light-gray'],
'columns' => [
    'fromDate:date',
    'toDate:date',
    'reason:raw',
    'fromTime:time',
    'toTime:time'
],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
<div class="clearfix"></div>
</div>
<?php
    Modal::begin([
        'header' => '<h4 class="m-0">Unavailability</h4>',
        'id'=>'unavailability-modal',
    ]);?>
	<div id="unavailability-content"></div>
	<?php Modal::end();?>
<script>
    $(document).on('click', '.add-unavailability, #unavailability-list  tbody > tr', function() {
        var unavailabilityId = $(this).data('key');
        var teacherId = '<?= $model->id;?>';
        if (unavailabilityId === undefined) {
            var customUrl = '<?= Url::to(['teacher-unavailability/create']); ?>?id=' + teacherId;
        } else {
            var customUrl = '<?= Url::to(['teacher-unavailability/update']); ?>?id=' + unavailabilityId;
        }
        $.ajax({
            url    : customUrl,
            type   : 'get',
            success: function(response)
            {
                if(response.status)
                {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                    if (unavailabilityId !== undefined) {
                        var params = $.param({ id: unavailabilityId });
                        var url    = '<?= Url::to(['teacher-unavailability/delete']) ?>?' + params;
                        $('.modal-delete').show();
                        $(".modal-delete").attr("action", url);
                    }
                    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Unavailability</h4>');
                    $('#popup-modal .modal-dialog').addClass('classroom-dialog');
                }
            }
        });
        return false;
    });
    
    $(document).off('click', '#unavailability-delete-button').on('click', '#unavailability-delete-button', function () {
        var unavailabilityId = $('#unavailability-list  tbody > tr').data('key');
        $.ajax({
            url    : '<?= Url::to(['teacher-unavailability/delete']); ?>?id=' + unavailabilityId,
            type   : 'get',
            success: function(response)
            {
                if(response.status)
                {
                    $.pjax.reload({container : '#unavailability-list', timeout : 6000});
                    $('#unavailability-modal').modal('hide');
                }
            }
        });
        return false;
    });
</script>