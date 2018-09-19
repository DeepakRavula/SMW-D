<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;
use common\components\gridView\AdminLteGridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\CitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cities';
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);

$addButton = Html::a(Yii::t('backend', '<i class="fa fa-plus f-s-18 m-l-10 aria-hidden="true"></i>'), '#', ['class' => 'add-city btn-sm']);
$this->params['action-button'] = $lastRole->name === User::ROLE_ADMINISTRATOR ? $addButton : null;
?>
<?php yii\widgets\Pjax::begin(['id' => 'city-listing']); ?>
<div>
    <?php echo AdminLteGridView::widget([
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
    ]); ?>

</div>
<?php Pjax::end(); ?>
<script>
    $(document).on('click', '.action-button,#city-listing  tbody > tr', function () {
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