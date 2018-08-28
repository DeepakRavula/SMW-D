<?php

use yii\helpers\Html;
use common\components\gridView\AdminLteGridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\widgets\Pjax;
use common\models\User;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Referral Source';
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);

$addButton = Html::a(Yii::t('backend', '<i class="fa fa-plus f-s-18 m-l-10 aria-hidden="true"></i>'), '#', ['class' => 'add-referral-sources btn-sm']);
$this->params['action-button'] = $lastRole->name === User::ROLE_ADMINISTRATOR ? $addButton : null;
$this->params['breadcrumbs'][] = $this->title;
?> 	

<div class="referral-sources-index "> 
    <?php Pjax::begin([
        'id' => 'referral-sources-listing',
        'timeout' => 6000
    ]); ?>
    <?php echo AdminLteGridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
        'tableOptions' => ['class' => 'table table-bordered m-0'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            'name',
        ],
    ]); ?> 
<?php Pjax::end(); ?>
</div> 
<script>
$(document).ready(function () {
    $(document).on('click', '.action-button,#referral-sources-listing  tbody > tr', function () {
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
                    }
                }
            });
            return false;
        });
    });
</script>