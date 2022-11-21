<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\User;
use yii\widgets\Pjax;
use common\models\Enrolment;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\GroupCourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>

<div class="pull-right">
    <a href="#" title="Add" id="student-enrol" class="add-new-lesson"><i class="fa fa-plus"></i></a>
</div>
<?php Pjax::begin(['id' => 'group-course-student']) ?>

<div class="group-course-student-index">
    <?php echo GridView::widget([
        'dataProvider' => $studentDataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'emptyText' => false,
        'columns' => [
            [
                'label' => 'Student Name',
                'format' => 'raw',
                'value' => function ($data) {
                    $url = Url::to(['/student/view', 'id' => $data->id]);
                    return Html::a($data->fullName, $url);
                },
            ],
            [
                'label' => 'Customer Name',
                'format' => 'raw',
                'value' => function ($data) {
                    $url = Url::to(['/user/view', 'UserSearch[role_name]' => User::ROLE_CUSTOMER, 'id' => $data->customer->id]);
                    return Html::a($data->customer->publicIdentity, $url);
                },
            ],
            [
                'label' => 'Discount',
                'value' => function ($data) use ($courseModel) {
                    $enrolment = Enrolment::find()
                        ->notDeleted()
                        ->isConfirmed()
                        ->andWhere(['studentId' => $data->id, 'courseId' => $courseModel->id])
                        ->one();
                    return $enrolment->groupDiscountValue;
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{edit} {mail} {print}',
                'buttons' => [
                    'edit' => function ($url, $model) use ($courseModel) {
                        $enrolment = Enrolment::find()
                            ->notDeleted()
                            ->isConfirmed()
                            ->andWhere(['studentId' => $model->id, 'courseId' => $courseModel->id])
                            ->one();
                        $url = Url::to(['group-enrolment/edit-discount', 'enrolmentId' => $enrolment->id]);
                        return Html::a('Edit Discount', '#', [
                            'title' => Yii::t('yii', 'Edit Discount'),
                            'class' => ['btn-info btn-sm group-enrolment-discount'],
                            'action-url' => $url
                        ]);
                    },
                    'mail' => function ($url, $model) use ($courseModel) {
                        $enrolment = Enrolment::find()
                            ->notDeleted()
                            ->isConfirmed()
                            ->andWhere(['studentId' => $model->id, 'courseId' => $courseModel->id])
                            ->one();
                        $url = Url::to(['email/group-enrolment-detail', 'enrolmentId' => $enrolment->id]);
                        return Html::a('<i title="Mail" class="fa fa-envelope"></i>', '#', [
                            'title' => Yii::t('yii', 'Mail'),
                            'class' => ['btn-info btn-sm group-enrolment-email'],
                            'action-url' => $url
                        ]);
                    },
                    'print' => function ($url, $model) use ($courseModel) {
                        $enrolment = Enrolment::find()
                            ->notDeleted()
                            ->isConfirmed()
                            ->andWhere(['studentId' => $model->id, 'courseId' => $courseModel->id])
                            ->one();
                        $url = Url::to(['print/group-enrolment', 'id' => $enrolment->id]);
                        return Html::a('<i title="Print" class="fa fa-print"></i>', '#', [
                            'title' => Yii::t('yii', 'Print'),
                            'class' => ['btn-info btn-sm group-enrolment-print'],
                            'action-url' => $url
                        ]);
                    }
                ]
            ]
        ]
    ]); ?>
</div>
<?php Pjax::end(); ?>

<script>
    $(document).off('click', '#student-enrol').on('click', '#student-enrol', function() {
        $.ajax({
            url: '<?= Url::to(['enrolment/group', 'GroupCourseForm[courseId]' => $courseModel->id]) ?>',
            type: 'get',
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                    $('.modal-save').show();
                    $('.modal-save').text('Confirm');
                    $('#popup-modal .modal-dialog').css({
                        'width': '600px'
                    });
                }
            }
        });
        return false;
    });

    $(document).off('click', '.group-enrolment-print').on('click', '.group-enrolment-print', function() {
        var url = $(this).attr('action-url');
        window.open(url, '_blank');
        return false;
    });

    $(document).off('click', '.group-enrolment-email').on('click', '.group-enrolment-email', function() {
        $.ajax({
            url: $(this).attr('action-url'),
            type: 'get',
            success: function(response) {
                if (response.status) {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                    $('.modal-save').text('save');
                    $('#popup-modal .modal-dialog').css({
                        'width': '600px'
                    });
                }
            }
        });
        return false;
    });

    $(document).off('click', '.group-enrolment-discount').on('click', '.group-enrolment-discount', function() {
        var url = $(this).attr('action-url');
        $.ajax({
            url: url,
            type: 'get',
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                    $('.modal-save').show();
                    $('.modal-save').text('save');
                    $('#popup-modal .modal-dialog').css({
                        'width': '400px'
                    });
                }
            }
        });
        return false;
    });
</script>