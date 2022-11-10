<?php 

use yii\widgets\Pjax;
use common\components\gridView\AdminLteGridView;
use yii\bootstrap\Modal;

?>

<div class="clearfix"></div>
<div class="grid-row-open">
	<?php 
        $columns = [
                [
                    'label' => 'Name',
                    'value' => function ($data) {
                        return !empty($data->user->publicIdentity) ? $data->user->publicIdentity : null;
                    },
                ],
                [
                    'label' => 'Email',
                    'value' => function ($data) {
                        return !empty($data->primaryEmail->email) ? $data->primaryEmail->email : null;
                    },
                ],
                [
                    'label' => 'Pin',
                    'value' => function ($data) {
                        return $data->pin;
                    },
                ],
                //['class' => 'yii\grid\ActionColumn', 'template' => '{update}']
            ];
        ?>
    <?php Pjax::begin([
        'id' => 'user-pin-listing',
        'timeout' => 6000,
    ]) ?>
    <?php echo AdminLteGridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => $columns,
    ]); ?>
	<?php Pjax::end(); ?>
    </div>

<?php
Modal::begin([
    'header' => '<h4 class="m-0">Edit Pin</h4>',
    'id' => 'edit-pin-modal',
    'toggleButton' => ['label' => 'click me', 'class' => 'hide'],
]); ?>
<div id="edit-pin-modal-content"></div>
<?php Modal::end();?>

<script>
    $(document).on('click', '.glyphicon-pencil', function () {
        $.ajax({
            url    : $(this).attr('href'),
            type: 'get',
            dataType: "json",
            success: function (response)
            {
                if (response.status)
                {
                    $('#edit-pin-modal').modal('show');
                    $('#edit-pin-modal-content').html(response.data);
                }
            }
        });
    });
</script>