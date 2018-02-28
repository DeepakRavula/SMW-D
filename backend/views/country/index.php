<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\models\User;
use common\components\gridView\AdminLteGridView;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\CountrySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Countries';
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);

$addButton = Html::a(Yii::t('backend', '<i class="fa fa-plus f-s-18 m-l-10" aria-hidden="true"></i>'), '#', ['class' => 'add-country']);
$this->params['action-button'] = $lastRole->name === User::ROLE_ADMINISTRATOR ? $addButton : null;
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
    <?php echo AdminLteGridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
        'emptyText' => false,
        'columns' => [
            'name',

        ],
    ]); ?>

</div>
<?php Pjax::end();?>
<script>
        $(document).on('click', '.action-button,#country-listing  tbody > tr', function () {
	    $('#popup-modal .modal-dialog').css({'width': '400px'});
            var countryId = $(this).data('key');
            if (countryId === undefined) {
                    var customUrl = '<?= Url::to(['country/create']); ?>';
                    $('#modal-delete').hide();
            } else {
                var customUrl = '<?= Url::to(['country/update']); ?>?id=' + countryId;
                var url = '<?= Url::to(['country/delete']); ?>?id=' + countryId;
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