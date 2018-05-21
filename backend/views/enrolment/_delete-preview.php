<?php

use yii\helpers\Url;
use kartik\grid\GridView;

?>

<?php yii\widgets\Pjax::begin([
    'id' => 'after-end-date-changed-listing',
    'timeout' => 6000,
]); ?>
<?php
$columns = [
    [
        'label' => 'Objects',
        'attribute' => 'objects',
        'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
    ],
    [
        'label' => 'Action',
        'attribute' => 'action',
        'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
    ],
    [
        'label' => 'Date Range',
        'attribute' => 'date_range',
        'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
    ]
];

$paymentColumns = [
    [
        'label' => 'Amount',
        'value' => 'amount',
        'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
    ],
    [
        'label' => 'Type',
        'value' => function ($data) {
            return $data->paymentMethod->name;
        },
        'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
    ],
    [
        'label' => 'Reference',
        'value' => function ($data) {
            return $data->reference;
        },
        'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
    ]
];
?>

<label>Enrolment Delete Preview</label>
<div class="row">
    <div class="col-lg-12">
        <?= GridView::widget([
            'dataProvider' => $previewDataProvider,
            'columns' => $columns,
            'summary' => false,
            'emptyText' => false
        ]); ?>
    </div>
</div>

<label>Payments to be deleted</label>
<div class="row">
    <div class="col-lg-12">
        <?= GridView::widget([
            'dataProvider' => $paymentsDataProvider,
            'columns' => $paymentColumns,
            'summary' => false,
            'emptyText' => false
        ]); ?>        
    </div>
</div>

<?php \yii\widgets\Pjax::end(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Enrolment Delete Preview</h4>');
        $('#popup-modal .modal-dialog').css({'width': '600px'});
        $('.modal-save').hide();
        $('.modal-delete').show();
        var url = "<?= Url::to(['enrolment/full-delete', 'id' => $model->id]); ?>";
        $('.modal-delete').attr('action', url);
    });
    $(document).on('modal-delete', function(event, params) {
        if (params.url) {
            window.location.href = params.url;
        }
    });
</script>