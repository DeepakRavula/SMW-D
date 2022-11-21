<?php

use yii\helpers\Html;
use common\components\gridView\KartikGridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use common\models\User;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);

$toolbar = [];
if ($lastRole->name === User::ROLE_ADMINISTRATOR ) {
    $toolbar [] = ['content' => Html::a('<i class="fa fa-plus"></i>', '#', [
        'class' => 'btn btn-success add-referral-sources'
    ]),'options' => ['title' =>'Add',
    'class' => 'btn-group mr-2']];
}
?> 	
<div id="error-notification" style="display:none;" class="alert-danger alert fade in"></div>
<div class="referral-sources-index "> 
    <?php Pjax::begin([
        'id' => 'referral-sources-listing',
        'timeout' => 6000
    ]); ?>
    <?= KartikGridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'tableOptions' => ['class' => 'table table-bordered m-0'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            'name',
        ],
        'toolbar' => $toolbar,
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Referral Source'
        ],
    ]); ?> 
<?php Pjax::end(); ?>
</div> 

<script>
$(document).ready(function () {
    $(document).on('click', '.add-referral-sources,#referral-sources-listing  tbody > tr', function () {
            var referralSourceId = $(this).data('key');
             if (referralSourceId === undefined) {
                    var customUrl = '<?= Url::to(['referral-source/create']); ?>';
            } else {
                var customUrl = '<?= Url::to(['referral-source/update']); ?>?id=' + referralSourceId;
                var url = '<?= Url::to(['referral-source/delete']); ?>?id=' + referralSourceId;
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
                        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Referral Sources</h4>');
			            $('#popup-modal .modal-dialog').css({'width': '400px'});
                        $('#modal-content').html(response.data);
                    } else {
                        $('#error-notification').html(response.message).fadeIn().delay(5000).fadeOut();
                    }
                }
            });
            return false;
        });
    });
</script>