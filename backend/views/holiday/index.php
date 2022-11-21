<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use common\components\gridView\KartikGridView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\HolidaySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
 <?php Modal::begin([
        'header' => '<h4 class="m-0">Holiday</h4>',
        'id' => 'holiday-modal',
    ]); ?>
<div id="holiday-content"></div>
 <?php  Modal::end(); ?>
<div>
<?php yii\widgets\Pjax::begin([
    'id' => 'holiday-listing'
]); ?>
    <?= KartikGridView::widget([
        'id' => 'holiday-grid',
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            [
                'attribute' => 'date',
                'label' => 'Date',
                'value' => function ($data) {
                    return !(empty($data->date)) ? Yii::$app->formatter->asDate($data->date) : null;
                },
            ],
            'description'
        ],
        'toolbar' => [
            ['content' => Html::a('<i class="fa fa-plus"></i>', '#', [
                'class' => 'btn btn-success', 'id' => 'add-holiday'
            ]),'options' => ['title' =>'Add',
            'class' => 'btn-group mr-2']],
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Holidays'
        ],
    ]); ?>
	<?php yii\widgets\Pjax::end(); ?>
</div>
<script>
    $(document).ready(function() {
        $(document).on('click', '#add-holiday, #holiday-listing  tbody > tr', function () {
            $('#holiday-modal .modal-dialog').css({'width': '400px'});
            var holidayId = $(this).data('key');
            if (holidayId === undefined) {
                var customUrl = '<?= Url::to(['holiday/create']); ?>';
            } else {
                var customUrl = '<?= Url::to(['holiday/update']); ?>?id=' + holidayId;
            }
            $.ajax({
                url    : customUrl,
                type   : 'post',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status)
                    {
                        $('#holiday-content').html(response.data);
                        $('#holiday-modal').modal('show');
                    }
                }
            });
            return false;
        });
        $(document).on('beforeSubmit', '#holiday-form', function () {
            $.ajax({
                url    : $(this).attr('action'),
                type   : 'post',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status) {
                        $.pjax.reload({container: '#holiday-listing', timeout: 6000});
                        $('#holiday-modal').modal('hide');
                    }
                }
            });
            return false;
        });
        $(document).on('click', '.holiday-cancel', function () {
            $('#holiday-modal').modal('hide');
            return false;
        });
    });
</script>
