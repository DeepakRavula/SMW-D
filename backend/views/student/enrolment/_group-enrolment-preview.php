<?php

use yii\bootstrap\ActiveForm;
use common\models\Course;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use backend\assets\CustomGridAsset;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\GroupCourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>

<?php $form = ActiveForm::begin([
    'action' => Url::to(['enrolment/group-confirm', 'enrolmentId' => $model->id]),
    'id' => 'modal-form'
]); ?>

<?php ActiveForm::end(); ?>
<style>
 .kv-page-summary > td {
    border-top:none;
    font-weight: bold;
    text-align: right;
}

</style>
<div class="user-create-index"> 
    <?php echo GridView::widget([
        'dataProvider' => $lessonDataProvider,
        'tableOptions' => ['class' => 'table table-condensed'],
        'rowOptions' => ['class' => 'group-lesson-discount'],
        'showPageSummary' => true,
        'summary' => false,
        'emptyText' => false,
        'options' => [
            'id' => 'group-lesson-listing'
        ],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            [
                'label' => 'Date/Time',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDateTime($data->date);
                },
            ],
            [
                'label' => 'Duration',
                'value' => function ($data) {
                    return (new \DateTime($data->duration))->format('H:i');
                },
            ],
            [
                'label' => 'Price',
                'value' => function ($data) {
                    return Yii::$app->formatter->asCurrency(round($data->grossPrice, 2));
                },
                'contentOptions' => ['style' => 'text-align:right'],
                'headerOptions' => ['style' => 'text-align:right'],
                'pageSummary' => true,
                'pageSummaryFunc' =>  function () use ($lessonDataProvider) {
                    $lessons = $lessonDataProvider->query->all();
                    $total = 0.00;
                    foreach ($lessons as $lesson) {
                        $total+= $lesson->grossPrice;
                    }
                    return Yii::$app->formatter->asCurrency(round($total, 2));
               }
            ],
            [
                'label' => 'Discount',
                'value' => function ($data) use ($model) {
                    return Yii::$app->formatter->asCurrency(round($data->getGroupDiscount($model), 2));
                },
                'contentOptions' => ['style' => 'text-align:right'],
                'headerOptions' => ['style' => 'text-align:right'],
                'pageSummary' => true,
                'pageSummaryFunc' =>  function () use ($lessonDataProvider, $model) {
                    $lessons = $lessonDataProvider->query->all();
                    $total = 0.00;
                    foreach ($lessons as $lesson) {
                        $total+= $lesson->getGroupDiscount($model);
                    }
                    return Yii::$app->formatter->asCurrency(round($total, 2));
               }
            ],
            [
                'label' => 'Total',
                'value' => function ($data) use ($model) {
                    return Yii::$app->formatter->asCurrency(round($data->getGroupSubTotal($model), 2));
                },
                'contentOptions' => ['style' => 'text-align:right'],
                'headerOptions' => ['style' => 'text-align:right'],
                'pageSummary' => true,
                'pageSummaryFunc' =>  function () use ($lessonDataProvider, $model) {
                    $lessons = $lessonDataProvider->query->all();
                    $total = 0.00;
                    foreach ($lessons as $lesson) {
                        $total+= $lesson->getGroupSubTotal($model);
                    }
                    return Yii::$app->formatter->asCurrency(round($total, 2));
               }
            ],
            [
                'contentOptions' => ['style' => 'width:80px;'],
                'class' => 'kartik\grid\ActionColumn',              
                'template' => '{update}', 
                'buttons' => [
                    'update' => function ($url, $lesson) use ($model) {
                        $action = Url::to(['group-lesson/apply-discount', 'GroupLessonDiscount[lessonId]' => $lesson->id, 'GroupLessonDiscount[enrolmentId]' => $model->id, 'GroupLessonDiscount[isPreview]' => true]);
                        return Html::a('', '#', [
                            'title' => Yii::t('yii', 'Update'),
                            'class' => ['glyphicon glyphicon-pencil group-lesson-discount-edit'],
                            'action-url' => $action
                        ]);
                    }
                ]
            ]
        ],
    ]); ?>
</div>


<script>
    $(document).ready(function () {
        $('.modal-save').show();
        $('.modal-cancel').show();
        $('#modal-back').hide();
        $('.modal-save').text('Confirm');
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Lesson Detail</h4>');
        $('#popup-modal .modal-dialog').css({'width': '800px'});
    });

    $(document).off('click', '.group-lesson-discount-edit').on('click', '.group-lesson-discount-edit', function () {
        var url =  $(this).attr('action-url');
        $.ajax({
            url    : url,
            type   : 'get',
            dataType: "json",
            success: function(response)
            {
                if(response.status)
                {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit Group Lesson Discount</h4>');
                    $('.modal-save').text('Save');
                }
            }
        });
        return false;
    });
</script>