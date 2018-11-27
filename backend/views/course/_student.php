<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\User;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\GroupCourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>

<div class="pull-right">
    <a href="#"  title="Add" id="student-enrol" class="add-new-lesson"><i class="fa fa-plus"></i></a>
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
            ]
        ],
    ]); ?>
</div>
<?php Pjax::end(); ?>

<script>
    $(document).off('click', '#student-enrol').on('click', '#student-enrol', function () {
        $.ajax({
            url    : '<?= Url::to(['enrolment/group', 'GroupCourseForm[courseId]' => $courseModel->id]) ?>',
            type: 'get',
            dataType: "json",
            success: function (response)
            {
                if (response.status) {
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
                    $('.modal-save').show();
                    $('.modal-save').text('Confirm');
                    $('#popup-modal .modal-dialog').css({'width': '600px'});
                } 
            }
        });
        return false;
    });
</script>