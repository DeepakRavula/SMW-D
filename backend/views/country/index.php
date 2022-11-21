<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use common\models\User;
use common\components\gridView\AdminLteGridView;
use common\components\gridView\KartikGridView;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\CountrySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);
$toolbar = [];
if ($lastRole->name === User::ROLE_ADMINISTRATOR ) {
    $toolbar [] = ['content' => Html::a('<i class="fa fa-plus"></i>', '#', [
        'class' => 'btn btn-success add-country'
    ]),'options' => ['title' =>'Add',
    'class' => 'btn-group mr-2']];
}
?>
<?php Modal::begin([
        'header' => '<h4 class="m-0">Country</h4>',
        'id' => 'country-modal',
    ]); ?>
<div id="country-content"></div>
 <?php  Modal::end(); ?>
<?php Pjax::Begin([
    'id' => 'country-listing'
]);?>
<div>
    <?php echo KartikGridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
        'emptyText' => false,
        'columns' => [
            'name',

        ],
        'toolbar' => $toolbar,
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Provinces'
        ],
    ]); ?>

</div>
<?php Pjax::end();?>
<script>
        $(document).on('click', '.add-country,#country-listing  tbody > tr', function () {
	    $('#popup-modal .modal-dialog').css({'width': '400px'});
            var countryId = $(this).data('key');
            if (countryId === undefined) {
                    var customUrl = '<?= Url::to(['country/create']); ?>';
            } else {
                var customUrl = '<?= Url::to(['country/update']); ?>?id=' + countryId;
                var url = '<?= Url::to(['country/delete']); ?>?id=' + countryId;
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
                        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Country</h4>');
                        $('#modal-content').html(response.data);
                    }
                }
            });
            return false;
        });
</script>