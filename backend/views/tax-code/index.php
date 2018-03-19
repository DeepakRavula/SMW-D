<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\components\gridView\AdminLteGridView;
use common\models\User;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TaxCodeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tax Codes';
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);

$addButton = Html::a(Yii::t('backend', '<i class="fa fa-plus f-s-18 m-l-10" aria-hidden="true"></i>'), ['create'], ['class' => 'add-taxcode']);
$this->params['action-button'] = $lastRole->name === User::ROLE_ADMINISTRATOR ? $addButton : null;
?>
<div>
<?php
Pjax::Begin([
    'id' => 'taxcode-listing'
]);

?>
    <?php echo AdminLteGridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            [
                'label' => 'Tax Name',
                'attribute' => 'tax_type_id',
                'value' => function ($data) {
                    return $data->taxType->name;
                },
            ],
            [
                'attribute' => 'province_id',
                'value' => function ($data) {
                    return $data->province->name;
                },
            ],
            [
                'label' => 'Rate (%)',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function ($data) {
                    return $data->rate;
                },
            ],
            'start_date:date',
            'code',
        ],
    ]); ?>
    
<?php Pjax::end();?>
</div>
<script>
        $(document).on('click', '.action-button, #taxcode-listing  tbody > tr', function () {
            var taxcodeId = $(this).data('key');
            if (taxcodeId === undefined) {
                var customUrl = '<?= Url::to(['tax-code/create']); ?>';
            } else {
                var customUrl = '<?= Url::to(['tax-code/update']); ?>?id=' + taxcodeId;
		var url = '<?= Url::to(['tax-code/delete']); ?>?id=' + taxcodeId;
		$('#modal-delete').show();
                $(".modal-delete").attr("action",url);
            }
            $.ajax({
                url    : customUrl,
                type   : 'get',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status) {
			    $('#popup-modal').modal('show');
			    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Tax Code</h4>');
			    $('#modal-content').html(response.data);                    }
			}
            });
            return false;
        });
</script>