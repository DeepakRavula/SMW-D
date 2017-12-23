<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;
use common\components\gridView\AdminLteGridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

use kartik\date\DatePickerAsset;
DatePickerAsset::register($this);

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Locations';
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);

$addButton = Html::a('<i class="fa fa-plus f-s-18 m-l-10" aria-hidden="true"></i>', ['create'], ['class' => 'add-location']);
$this->params['action-button'] = $lastRole->name === User::ROLE_ADMINISTRATOR ? $addButton : null;
?>
<?php Modal::begin([
        'header' => '<h4 class="m-0">Location</h4>',
        'id' => 'location-modal',
    ]); ?>
<div id="location-content"></div>
 <?php  Modal::end(); ?>
<?php Pjax::begin([
	'id' => 'location-listing',
]); ?>
<div class="grid-row-open p-10">
    <?php echo AdminLteGridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
    'rowOptions' => function ($model, $key, $index, $grid) {
        $url = Url::to(['location/view', 'id' => $model->id]);

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
    ]); ?>

</div>
<?php Pjax::end(); ?>
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
});
</script>
