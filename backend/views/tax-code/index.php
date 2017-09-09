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

$addButton = Html::a(Yii::t('backend', '<i class="fa fa-plus-circle" aria-hidden="true"></i> Add'), ['create'], ['class' => 'add-taxcode btn btn-primary btn-sm']);
$this->params['action-button'] = $lastRole->name === User::ROLE_ADMINISTRATOR ? $addButton : null;
?>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Tax Code</h4>',
    'id' => 'taxcode-modal',
]);

?>
<div id="taxcode-content"></div>
    <?php Modal::end(); ?>
<div>
<?php
Pjax::Begin([
    'id' => 'taxcode-listing'
]);

?>
    <?php echo AdminLteGridView::widget([
        'dataProvider' => $dataProvider,
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
            'rate',
            'start_date:date',
            'code',        
        ],
    ]); ?>
    
<?php Pjax::end();?>
</div>
<script>
    $(document).ready(function() {
        $(document).on('click', '.add-taxcode, #taxcode-listing  tbody > tr', function () {
            var taxcodeId = $(this).data('key');
            if (taxcodeId === undefined) {
                var customUrl = '<?= Url::to(['tax-code/create']); ?>';
            } else {
                var customUrl = '<?= Url::to(['tax-code/update']); ?>?id=' + taxcodeId;
            }
            $.ajax({
                url    : customUrl,
                type   : 'post',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status)
                    {
                        $('#taxcode-content').html(response.data);
                        $('#taxcode-modal').modal('show');
                    }
                }
            });
            return false;
        });
        $(document).on('beforeSubmit', '#taxcode-form', function () {
            $.ajax({
                url    : $(this).attr('action'),
                type   : 'post',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status) {
                        $.pjax.reload({container: '#taxcode-listing', timeout: 6000});
                        $('#taxcode-modal').modal('hide');
                    }
                }
            });
            return false;
        });
        $(document).on('click', '.taxcode-cancel', function () {
            $('#taxcode-modal').modal('hide');
            return false;
        });
    });
</script>