<?php

use common\models\User;
use common\models\Invoice;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use common\components\gridView\AdminLteGridView;
use kartik\select2\Select2Asset;

Select2Asset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$roles = ArrayHelper::getColumn(Yii::$app->authManager->getRoles(), 'description');
foreach ($roles as $name => $description) {
    if ($name === $searchModel->role_name) {
        $role = $description;
        break;
    }
}
$roleName = $searchModel->role_name;
$originalInvoice = Invoice::TYPE_INVOICE;
$this->title = Yii::t('backend', !isset($role) ? 'User' : $role.'s');
$this->params['action-button'] = Html::a(Yii::t('backend', '<i class="fa fa-plus f-s-18 m-l-10" aria-hidden="true"></i>'), '#', ['class' => 'f-s-18 add-user']);
$this->params['show-all'] = $this->render('_button', [
    'searchModel' => $searchModel
]);
?>
 

<div class="user-index"> 
<div class="grid-row-open">
    <?php yii\widgets\Pjax::begin([
	'enablePushState' => false,
        'id' => 'user-index',
        'timeout' => 6000
    ]); ?>
        <?= AdminLteGridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,
            'emptyText' => false,
            'rowOptions' => function ($model, $key, $index, $grid) use ($searchModel, $roleName, $originalInvoice) {
                $url = Url::to(['user/view', 'UserSearch[role_name]' => $roleName, 'id' => $model->id]);
                $data = ['data-url' => $url];
                if ($searchModel->showAll) {
                    if ((int)$model->status === User::STATUS_NOT_ACTIVE) {
                        $data = array_merge($data, ['class' => 'danger inactive']);
                    } elseif ((int)$model->status === User::STATUS_ACTIVE) {
                        $data = array_merge($data, ['class' => 'info active']);
                    }
                }

                return $data;
            },
            'tableOptions' => ['class' => 'table table-bordered'],
            'headerRowOptions' => ['class' => 'bg-light-gray'],
            'filterModel' => $searchModel,
            'columns' => [
            [
                'attribute' => 'firstname',
                'label' => 'First Name',
                'value' => function ($data) {
                    return !empty($data->userProfile->firstname) ? $data->userProfile->firstname : null;
                },
            ],
            [
                'attribute' => 'lastname',
                'label' => 'Last Name',
                'value' => function ($data) {
                    return !empty($data->userProfile->lastname) ? $data->userProfile->lastname : null;
                },
            ],
            'email',
            [
		'attribute' => 'phone',
                'label' => 'Phone',
                'value' => function ($data) {
                    return !empty($data->phoneNumber->number) ? $data->phoneNumber->number : null;
                },
            ]
        ],
    ]); ?>
<?php yii\widgets\Pjax::end(); ?>
</div>
</div>
<script>
    $(document).on('click', '.add-user', function() {
        var params = $.param({ 'role_name': '<?= $searchModel->role_name ?>' });
        $.ajax({
            url    : '<?= Url::to(['user/create']) ?>?' +params,
            type   : 'get',
            success: function(response)
            {
                if (response.status) {
                    $('#popup-modal').modal('show');
                    $('#popup-modal .modal-dialog').css({'width': '400px'});
                    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">' + '<?= $role ?>' + 's / Add</h4>');
                    $('#modal-content').html(response.data);
                }
            }
        });
        return false;
    });
$(document).ready(function(){
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    
   $("#usersearch-showall").on("change", function() {
      var showAll = $(this).is(":checked");
      var role_name= "<?=$roleName?>";
      var firstname_search = $("input[name*='UserSearch[firstname]").val();
      var lastname_search  = $("input[name*='UserSearch[lastname]").val();
      var email_search     = $("input[name*='UserSearch[email]").val();
      var phone_search     = $("input[name*='UserSearch[phone]").val();
      var params           = $.param({'UserSearch[role_name]': role_name, 'UserSearch[showAll]': (showAll | 0),'UserSearch[firstname]':firstname_search,'UserSearch[lastname]':lastname_search,'UserSearch[email]':email_search,'UserSearch[phone]':phone_search });
     var url = "<?php echo Url::to(['user/index']); ?>?"+params;
      $.pjax.reload({url:url,container:"#user-index",replace:false,  timeout: 6000});  //Reload GridView
    });
});
</script>
