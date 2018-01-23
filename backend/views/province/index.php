<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\components\gridView\AdminLteGridView;
use common\models\User;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ProvinceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Provinces';
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);

$addButton = Html::a(Yii::t('backend', '<i class="fa fa-plus f-s-18 m-l-10" aria-hidden="true"></i> Add'), '#', ['class' => 'add-province']);
$this->params['action-button'] = $lastRole->name === User::ROLE_ADMINISTRATOR ? $addButton : null;
?>
<?php Modal::begin([
        'header' => '<h4 class="m-0">Province</h4>',
        'id' => 'province-modal',
    ]); ?>
<div id="province-content"></div>
 <?php  Modal::end(); ?>
<?php Pjax::begin([
    'id' => 'province-listing'
]);?>
<div>
    <?php echo AdminLteGridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
        'emptyText' => false,
        'columns' => [
            'name',
            [
                'label' => 'Tax Rate (%)',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function ($data) {
                    return $data->tax_rate;
                },
            ],
            [
                'label' => 'Country',
                'value' => function ($data) {
                    return !empty($data->country->name) ? $data->country->name : null;
                },
            ],
        ],
    ]); ?>

</div>
<?php Pjax::end(); ?>
<script>
    $(document).ready(function() {
        $(document).on('click', '.add-province, #province-listing  tbody > tr', function () {
            var provinceId = $(this).data('key');
            if (provinceId === undefined) {
                var customUrl = '<?= Url::to(['province/create']); ?>';
            } else {
                var customUrl = '<?= Url::to(['province/update']); ?>?id=' + provinceId;
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
                        $('#province-content').html(response.data);
                        $('#province-modal').modal('show');
                    }
                }
            });
            return false;
        });
        $(document).on('beforeSubmit', '#province-form', function () {
            $.ajax({
                url    : $(this).attr('action'),
                type   : 'post',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status) {
                        $.pjax.reload({container: '#province-listing', timeout: 6000});
                        $('#province-modal').modal('hide');
                    }
                }
            });
            return false;
        });
        $(document).on('click', '.province-cancel', function () {
            $('#province-modal').modal('hide');
            return false;
        });
    });
</script>