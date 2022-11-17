<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\components\gridView\KartikGridView;
use kartik\grid\GridView;
use common\models\User;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ProvinceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);

$toolbar = [];
if ($lastRole->name === User::ROLE_ADMINISTRATOR ) {
    $toolbar [] = ['content' => Html::a('<i class="fa fa-plus"></i>', '#', [
        'class' => 'btn btn-success add-province'
    ]),'options' => ['title' =>'Add',
    'class' => 'btn-group mr-2']];
}
?>
<?php Pjax::begin([
    'id' => 'province-listing'
]);?>
<div>
    <?php echo KartikGridView::widget([
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
        'toolbar' => $toolbar,
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Provinces'
        ],
    ]); ?>

</div>
<?php Pjax::end(); ?>
<script>
        $(document).on('click', '.add-province,#province-listing  tbody > tr', function () {
            $('#popup-modal .modal-dialog').css({'width': '350px'});
            var provinceId = $(this).data('key');
             if (provinceId === undefined) {
                    var customUrl = '<?= Url::to(['province/create']); ?>';
            } else {
                var customUrl = '<?= Url::to(['province/update']); ?>?id=' + provinceId;
                var url = '<?= Url::to(['province/delete']); ?>?id=' + provinceId;
                $('.modal-delete').show();
                $(".modal-delete").attr("action",url);
            }
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
                        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Province</h4>');
                        $('#modal-content').html(response.data);
                    }
                }
            });
            return false;
        });
</script>