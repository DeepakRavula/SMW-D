<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use common\components\gridView\KartikGridView;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
    <div class="col-md-12">
        <div class="col-md-10">
<div id="index-success-notification" style="display:none;" class="alert-success alert fade in"></div>
<div id="index-error-notification" style="display:none;" class="alert-danger alert fade in"></div>
        </div>
<div class="pull-right" style="margin-left:50px">
    <div class="btn-group">
        <button class="btn dropdown-toggle" data-toggle="dropdown">Bulk Action&nbsp;&nbsp;<span class="caret"></span></button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li><a id="substitute-teacher-group-lesson" href="#">Substitute Teacher</a></li>
        </ul>
    </div>
     <a href="#"  title="Add" id="new-lesson" class="add-new-lesson"><i class="fa fa-plus"></i></a>
</div>
        
    </div>
 <div class="col-md-12">
<div class="grid-row-open p-10">
<?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
    <?php $columns = [
        [
                'class' => '\kartik\grid\CheckboxColumn',
                'mergeHeader' => false
        ],
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
                            if (!$model->hasGroupInvoice() && $model->isScheduledOrRescheduled()) {
                                return Html::a('Create Invoice', $url, [
                                    'class' => ['btn-success btn-sm']
                                ]);
                            } else {
                                return null;
                            }
                        },
                        'view' => function ($url, $model) {
                            $url = Url::to(['lesson/view', 'id' => $model->id, '#' => 'student']);
                            if ($model->hasGroupInvoice() && $model->isScheduledOrRescheduled()) {
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
        'options' => ['id' => 'lesson-index-1'],
        'columns' => $columns,
    ]); ?>
	<?php yii\widgets\Pjax::end(); ?>

</div>
 </div>
<?php Modal::begin([
        'header' => '<h4 class="m-0">Substitute Teacher</h4>',
        'id'=>'teacher-substitute-modal',
]);?>
<div id="teacher-substitute-content"></div>
<?php Modal::end(); ?>

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
$(document).on('click', '#substitute-teacher-group-lesson', function(){
alert('success begin');
        var lessonIds = $('#lesson-index-1').yiiGridView('getSelectedRows');
        if ($.isEmptyObject(lessonIds)) {
            $('#index-error-notification').html("Choose any lessons to substitute teacher").fadeIn().delay(5000).fadeOut();
        } else {
          var params = $.param({ ids: lessonIds });
            $.ajax({
                url    : '<?= Url::to(['teacher-substitute/index']) ?>?' +params,
                type   : 'get',
                success: function(response)
                {
                    if (response.status) {
                        alert('sucess');
                        $('#teacher-substitute-modal').modal('show');
                        $('#teacher-substitute-modal .modal-dialog').css({'width': '1000px'});
                        $('#teacher-substitute-content').html(response.data);
                    } else {
                        $('#index-error-notification').html("Choose lessons with same teacher").fadeIn().delay(5000).fadeOut();
                    }
                }
            });
            return false;
        }
    });
</script>