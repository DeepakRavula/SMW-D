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
                if ($searchModel->showAllCustomers || $searchModel->showAllTeachers || $searchModel->showAllAdministrators||$searchModel->showAllOwners||$searchModel->showAllStaffMembers) {
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
    $(document).on('modal-success', function(event, params) {
        window.location.href = params.url;
        return false;
    });
    
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
    
  $("#usersearch-showallcustomers").on("change", function() {
      var showAllCustomers = $(this).is(":checked");
      var url = "<?php echo Url::to(['user/index', 'UserSearch[role_name]' => User::ROLE_CUSTOMER]); ?>&UserSearch[query]=" + "<?php echo $searchModel->query; ?>&UserSearch[showAllCustomers]=" + (showAllCustomers | 0);
      $.pjax.reload({url:url,container:"#user-index",replace:false,  timeout: 6000});  //Reload GridView
    });
	 $("#usersearch-showallteachers").on("change", function() {
      var showAllTeachers = $(this).is(":checked");
      var url = "<?php echo Url::to(['user/index', 'UserSearch[role_name]' => User::ROLE_TEACHER]); ?>&UserSearch[query]=" + "<?php echo $searchModel->query; ?>&UserSearch[showAllTeachers]=" + (showAllTeachers | 0);
      $.pjax.reload({url:url,container:"#user-index",replace:false,  timeout: 6000});  //Reload GridView
    });
    $("#usersearch-showalladministrators").on("change", function() {
      var showAllAdministrators = $(this).is(":checked");
      var url = "<?php echo Url::to(['user/index', 'UserSearch[role_name]' => User::ROLE_ADMINISTRATOR]); ?>&UserSearch[query]=" + "<?php echo $searchModel->query; ?>&UserSearch[showAllAdministrators]=" + (showAllAdministrators | 0);
      $.pjax.reload({url:url,container:"#user-index",replace:false,  timeout: 6000});  //Reload GridView
    });
    $("#usersearch-showallstaffmembers").on("change", function() {
      var showAllStaffMembers = $(this).is(":checked");
      var url = "<?php echo Url::to(['user/index', 'UserSearch[role_name]' => User::ROLE_STAFFMEMBER]); ?>&UserSearch[query]=" + "<?php echo $searchModel->query; ?>&UserSearch[showAllStaffMembers]=" + (showAllStaffMembers | 0);
      $.pjax.reload({url:url,container:"#user-index",replace:false,  timeout: 6000});  //Reload GridView
    });
        $("#usersearch-showallowners").on("change", function() {
      var showAllOwners = $(this).is(":checked");
      var url = "<?php echo Url::to(['user/index', 'UserSearch[role_name]' => User::ROLE_OWNER]); ?>&UserSearch[query]=" + "<?php echo $searchModel->query; ?>&UserSearch[showAllOwners]=" + (showAllOwners | 0);
      $.pjax.reload({url:url,container:"#user-index",replace:false,  timeout: 6000});  //Reload GridView
    });
});
</script>
