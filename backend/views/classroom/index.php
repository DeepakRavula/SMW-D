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
<?php yii\widgets\Pjax::begin([
    'id' => 'classroom-listing'
]); ?>
<div class="grid-row-open">
    <?php echo AdminLteGridView::widget([
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
