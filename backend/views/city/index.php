<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use common\components\gridView\KartikGridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\CitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);

// $addButton = Html::a(Yii::t('backend', '<i class="fa fa-plus f-s-18 m-l-10 aria-hidden="true"></i>'), '#', ['class' => 'add-city btn-sm']);
// $this->params['action-button'] = $lastRole->name === User::ROLE_ADMINISTRATOR ? $addButton : null;

$toolbar = [];
if ($lastRole->name === User::ROLE_ADMINISTRATOR ) {
    $toolbar [] = ['content' => Html::a('<i class="fa fa-plus"></i>', '#', [
        'class' => 'btn btn-success add-city'
    ]),'options' => ['title' =>'Add',
    'class' => 'btn-group mr-2']];
}
?>
<?php yii\widgets\Pjax::begin(['id' => 'city-listing']); ?>
<div>
    <?php echo KartikGridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
        'emptyText' => false,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            [
                'attribute' => 'name',
                'label' => 'Name',
                'value' => function ($data) {
                    return !empty($data->name) ? $data->name : null;
                },
            ],
            [
                'attribute' => 'province_id',
                'label' => 'Province',
                'value' => function ($data) {
                    return !empty($data->province->name) ? $data->province->name : null;
                },
            ],
        ],
        'toolbar' =>  $toolbar,
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Cities'
        ],
    ]); ?>

</div>
<?php Pjax::end(); ?>
<script>
    $(document).on('click', '.add-city,#city-listing  tbody > tr', function () {
            var cityId = $(this).data('key');
             if (cityId === undefined) {
                    var customUrl = '<?= Url::to(['city/create']); ?>';
            } else {
                var customUrl = '<?= Url::to(['city/update']); ?>?id=' + cityId;
                var url = '<?= Url::to(['city/delete']); ?>?id=' + cityId;
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
                        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">City</h4>');
			            $('#popup-modal .modal-dialog').css({'width': '400px'});
                        $('#modal-content').html(response.data);
                    }
                }
            });
            return false;
        });
</script>