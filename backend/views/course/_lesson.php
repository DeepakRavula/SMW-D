<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="pull-right m-r-10">
    <a href="#"  title="Add" id="new-lesson" class="add-new-lesson text-add-new"><i class="fa fa-plus"></i></a>
</div>
<div class="grid-row-open p-10">
<?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
    <?php $columns = [
        [
            'label' => 'Date',
            'value' => function ($data) {
                $date = Yii::$app->formatter->asDate($data->date);
                $lessonTime = (new \DateTime($data->date))->format('H:i:s');

                return !empty($date) ? $date.' @ '.Yii::$app->formatter->asTime($lessonTime) : null;
            },
        ],
        [
            'label' => 'Status',
            'value' => function ($data) {
                $status = null;
                if (!empty($data->status)) {
                    return $data->getStatus();
                }

                return $status;
            },
        ],
                ['class' => 'yii\grid\ActionColumn',
                    'template' => '{create} {view}',
                    'buttons' => [
                        'create' => function ($url, $model) {
                            $url = Url::to(['invoice/group-lesson', 'lessonId' => $model->id, 'enrolmentId' => null]);
                            if (!$model->hasGroupInvoice() && $model->isScheduled()) {
                                return Html::a('Create Invoice', $url, [
                                    'class' => ['btn-success btn-sm']
                                ]);
                            } else {
                                return null;
                            }
                        },
                        'view' => function ($url, $model) {
                            $url = Url::to(['lesson/view', 'id' => $model->id, '#' => 'student']);
                            if ($model->hasGroupInvoice() && $model->isScheduled()) {
                                return Html::a('View Invoice', $url, [
                                    'class' => ['btn-info btn-sm']
                                ]);
                            } else {
                                return null;
                            }
                        }
                    ]
                ],
    ];
     ?>   
    <?php echo GridView::widget([
        'dataProvider' => $lessonDataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['lesson/view', 'id' => $model->id]);

            return ['data-url' => $url];
        },
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'emptyText' => false,
        'columns' => $columns,
    ]); ?>
	<?php yii\widgets\Pjax::end(); ?>

</div>

<script>
$(document).on('click', '#new-lesson', function () {
    $.ajax({
        url    : '<?= Url::to(['extra-lesson/create-group', 'courseId' => $courseModel->id]); ?>',
        type   : 'get',
        dataType: "json",
        success: function(response)
        {
           if(response.status)
           {
                $('#modal-content').html(response.data);
                $('#popup-modal').modal('show');
                $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Add Lesson</h4>');
                $('#popup-modal .modal-dialog').css({'width': '1000px'});
            }
        }
    });
    return false;
});
</script>