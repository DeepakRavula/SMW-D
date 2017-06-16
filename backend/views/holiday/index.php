<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\models\User;
use yii\bootstrap\Modal;
use common\models\Holiday;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\HolidaySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Holidays';
?>
<?php $this->params['action-button'] = Html::a(Yii::t('backend', '<i class="fa fa-plus-circle" aria-hidden="true"></i> Add'), '#', ['class' => 'btn btn-primary btn-sm add-holiday']);?>
 <?php Modal::begin([
        'header' => '<h4 class="m-0">Holiday</h4>',
        'id' => 'holiday-modal',
    ]); ?>
<div id="holiday-content">
	<?php echo $this->render('_form', [
        'model' => new Holiday(),
    ]); ?>
	</div>
 <?php  Modal::end(); ?>
<div>
<?php yii\widgets\Pjax::begin([
	'id' => 'holiday-grid'
]); ?>
    <?php echo GridView::widget([
		'id' => 'holiday-listing',
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            [
                'attribute' => 'date',
                'label' => 'Date',
                'value' => function ($data) {
                    return !(empty($data->date)) ? Yii::$app->formatter->asDate($data->date) : null;
                },
            ],
			'description'
        ],
    ]); ?>
	<?php yii\widgets\Pjax::end(); ?>
</div>
<script>
	 $(document).ready(function() {
		$('.add-holiday').click(function() {
			$('#holiday-modal').modal('show');	
			return false;
		});	
		$(document).on('beforeSubmit', '#holiday-form', function () {
            $.ajax({
                url    : $(this).attr('action'),
                type   : 'post',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status) {
                        $.pjax.reload({container: '#holiday-grid', timeout: 6000});
                        $('#holiday-modal').modal('hide');
                    }
                }
            });
            return false;
        });
		   $(document).on('click', '#holiday-listing  tbody > tr', function () {
            var holidayId = $(this).data('key');
            var url = '<?= Url::to(['holiday/update']); ?>?id=' + holidayId;
            $.ajax({
                url    : url,
                type   : 'post',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status)
                    {
                        $('#holiday-content').html(response.data);
                        $('#holiday-modal').modal('show');
                    }
                }
            });
            return false;
        });
        $(document).on('click', '.holiday-cancel', function () {
            $('#holiday-modal').modal('hide');
            return false;
        });
	});
</script>