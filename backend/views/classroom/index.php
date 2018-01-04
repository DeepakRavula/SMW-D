<?php

use yii\helpers\Html;
use common\components\gridView\AdminLteGridView;
use common\models\Classroom;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Classrooms';

$addButton = Html::a('<i class="fa fa-plus" aria-hidden="true"></i>', '#', ['class' => 'f-s-18', 'id' => 'add-classroom']);
$this->params['action-button'] = $addButton;
?>
<?php Modal::begin([
        'header' => '<h4 class="m-0">Classroom</h4>',
        'id' => 'classroom-modal',
    ]); ?>
<?= $this->render('_form', [
	'model' => new Classroom(),
]);?>
 <?php  Modal::end(); ?>
<?php yii\widgets\Pjax::begin([
	'id' => 'classroom-listing'
]); ?>
<div class="grid-row-open">
    <?php echo AdminLteGridView::widget([
        'dataProvider' => $dataProvider,
		'rowOptions' => function ($model, $key, $index, $grid){
            $url = Url::to(['classroom/view', 'id' => $model->id]);
            $data = ['data-url' => $url];
            return $data;
        },
		'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'emptyText' => false,
        'columns' => [
            'name',
        ],
    ]); ?>
</div>
<?php yii\widgets\Pjax::end(); ?>
<script>
    $(document).ready(function() {
        $(document).on('click', '#add-classroom', function () {
           	$('#classroom-modal').modal('show'); 
           	$('#classroom-modal .modal-dialog').addClass('classroom-dialog'); 
            return false;
        });
        $(document).on('click', '#classroom-cancel', function () {
            $('#classroom-modal').modal('hide');
            return false;
        });
		$(document).on('beforeSubmit', '#classroom-form', function () {
            $.ajax({
                url    : $(this).attr('action'),
                type   : 'post',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status) {
                        $.pjax.reload({container: '#classroom-listing', timeout: 6000});
                        $('#classroom-modal').modal('hide');
                    }
                }
            });
            return false;
        });
    });
</script>
