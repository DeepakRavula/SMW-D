<?php

use yii\helpers\Html;
use common\components\gridView\AdminLteGridView;
use common\components\gridView\KartikGridView;
use common\models\Classroom;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<?php yii\widgets\Pjax::begin([
    'id' => 'classroom-listing'
]); ?>
<div class="grid-row-open">
    <?= KartikGridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
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
            'description',
        ],
        'toolbar' =>  [
            [
            'content' =>
                Html::a('<i class="fa fa-plus"></i>', '#', [
                    'class' => 'btn btn-success', 'id' => 'add-classroom',
                ]),
            'options' => ['title' =>'Add',
                          'class' => 'btn-group mr-2']
            ],
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Classrooms'
        ],
    ]); ?>
</div>
<?php yii\widgets\Pjax::end(); ?>
<script>
    $(document).on('click', '#add-classroom', function () {
        var customUrl = '<?= Url::to(['classroom/create']); ?>';
            $.ajax({
                url    : customUrl,
                type   : 'get',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status)
                    {
                        $('#popup-modal').modal('show');
                        $('#modal-content').html(response.data);
                    }
                }
            });
        return false;
    });
</script>
