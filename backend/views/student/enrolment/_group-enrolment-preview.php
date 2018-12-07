<?php

use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use common\models\Course;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\GroupCourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>

<?php $form = ActiveForm::begin([
    'action' => Url::to(['enrolment/group-confirm', 'enrolmentId' => $model->id]),
    'id' => 'modal-form'
]); ?>

<?php ActiveForm::end(); ?>

<div class="user-create-index"> 
    <?php echo GridView::widget([
        'dataProvider' => $lessonDataProvider,
        'tableOptions' => ['class' => 'table table-condensed'],
        'rowOptions' => ['class' => 'group-lesson-discount'],
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
            ],
            [
                'label' => 'Discount',
                'value' => function ($data) use ($model) {
                    return Yii::$app->formatter->asCurrency(round($data->getGroupDiscount($model), 2));
                },
            ],
            [
                'label' => 'Total',
                'value' => function ($data) use ($model) {
                    return Yii::$app->formatter->asCurrency(round($data->getGroupSubTotal($model), 2));
                },
            ],
            [
                'contentOptions' => ['style' => 'width:80px;'],
                'class' => 'yii\grid\ActionColumn',              
                'template' => '{update}', 
                'buttons' => [
                    'update' => function ($url, $lesson) use ($model) {
                        $action = Url::to(['group-lesson/apply-discount', 'GroupLesson[lessonId]' => $lesson->id, 'GroupLesson[enrolmentId]' => $model->id, 'GroupLesson[isPreview]' => true]);
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
        $('#modal-back').hide();
        $('.modal-save').text('Confirm');
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Lesson Detail</h4>');
        $('#popup-modal .modal-dialog').css({'width': '800px'});
    });

    $(document).on('click', '.group-lesson-discount-edit', function () {
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