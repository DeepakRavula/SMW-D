<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;
use common\components\gridView\AdminLteGridView;
use common\components\gridView\KartikGridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

use kartik\date\DatePickerAsset;
use kartik\grid\GridView;

DatePickerAsset::register($this);

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Locations';
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);

$toolbar = [];
if ($lastRole->name === User::ROLE_ADMINISTRATOR ) {
    $toolbar [] = ['content' => Html::a('<i class="fa fa-plus"></i>', '#', [
        'class' => 'btn btn-success add-location'
    ]),'options' => ['title' =>'Add',
    'class' => 'btn-group mr-2']];
}
?>
<?php Modal::begin([
        'header' => '<h4 class="m-0">Location</h4>',
        'id' => 'location-modal',
    ]); ?>
<div id="location-content"></div>
 <?php  Modal::end(); ?>
<div class="grid-row-open p-10">
<?php Pjax::begin([
    'id' => 'location-listing',
]); ?>
    <?= KartikGridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
    'rowOptions' => function ($model, $key, $index, $grid) {
        $url = Url::to(['/location-view', 'location' => $model->slug]);

        return ['data-url' => $url];
    },
        'columns' => [
            [
                'attribute' => 'name',
                'label' => 'Name',
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->name;
                },
            ],
            'address',
            'email',
        ],
        'toolbar' => $toolbar,
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Locations'
        ],
    ]); ?>
<?php Pjax::end(); ?>
</div>
<script>
$(document).ready(function() {
	$(document).on('click', '.add-location', function () {
		$.ajax({
			url    : '<?= Url::to(['location/create']); ?>',
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
				if(response.status)
				{
					$('#location-content').html(response.data);
					$('#location-modal').modal('show');
				} 
			}
		});
		return false;
	});
		$(document).on('beforeSubmit', '#location-form', function () {
        $.ajax({
            url    : $(this).attr('action'),
            type   : 'post',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status) {
        			
                } else {
					$('#location-form').yiiActiveForm('updateMessages',
					response.errors, true);
				}
            }
        });
        return false;
    });
	$(document).on('click', '.location-cancel', function () {
		$('#location-modal').modal('hide');
		return false;
	});
});
</script>
