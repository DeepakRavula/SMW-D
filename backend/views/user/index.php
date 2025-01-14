<?php

use common\models\User;
use common\models\UserProfile;
use common\models\Location;
use common\models\Invoice;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use common\components\gridView\AdminLteGridView;
use kartik\select2\Select2Asset;
use common\components\gridView\KartikGridView;
use kartik\grid\GridView;
use backend\models\search\UserSearch;

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
$locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
$user = User::findOne(['id' => Yii::$app->user->id]);
?>
 

<div class="user-index"> 
<div class="grid-row-open">
    <?php yii\widgets\Pjax::begin([
	'enablePushState' => false,
        'id' => 'user-index',
        'timeout' => 6000
    ]); ?>
        <?php 
            $columns = [
            [
                'attribute' => 'firstname',
                'label' => 'First Name',
                'value' => function ($data) {
                    return !empty($data->userProfile->firstname) ? $data->userProfile->firstname : null;
                },
                'contentOptions' => ['style' => 'width:12%'],
            ],
            [
                'attribute' => 'lastname',
                'label' => 'Last Name',
                'value' => function ($data) {
                    return !empty($data->userProfile->lastname) ? $data->userProfile->lastname : null;
                },
                'contentOptions' => ['style' => 'width:12%'],
            ],
        ];
        array_push($columns,[
            'attribute' => 'email',
            'label' => 'Email',
            'value' => function ($data) {
                return !empty($data->primaryEmail->email) ? $data->primaryEmail->email : null;
            },
            'contentOptions' => ['style' => 'width:25%'],
        ]);
        if ($roleName == User::ROLE_TEACHER){
            array_push($columns,
            [
                'attribute' => 'phone',
                'label' => 'Phone',
                'value' => function ($data) {
                    return !empty($data->phoneNumber->number) ? $data->phoneNumber->number : null;
                },
                'contentOptions' => ['style' => 'width:15%'],
            ]);
        }     
    
            if ($roleName == User::ROLE_CUSTOMER) {
                array_push($columns,[
                    'attribute' => 'student',
                    'label' => 'Student',
                    'value' => function ($data) {
                        return !empty($data->student) ? $data->getStudentsList() : null;
                    },
                    'contentOptions' => ['style' => 'width:26%']
                ], 
                [
                    'attribute' => 'status',
                    'filter'=> UserSearch::balanceStatus(),
                    'label' => 'Balance',
                    'value' => function ($data, $key, $index, $widget) use(&$total) {
                            $total += round($data->customerAccount->balance, 2);
                            $widget->footer = Yii::$app->formatter->asCurrency($total);
                            return Yii::$app->formatter->asCurrency(round($data->customerAccount->balance, 2));
                },
                    'contentOptions' => ['class' => 'text-right', 'style' => 'width:20%'],
                    'hAlign' => 'right',
               ]
            );
            }
        ?>
        <?= KartikGridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => "Showing {begin} - {end} of {totalCount} items",
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
            'showFooter' => true,
            'columns' => $columns,
            'toolbar' =>  [
                [
                    'content' =>
                        Html::button('<i class="glyphicon glyphicon-plus"></i>', [
                            'type'=>'button', 
                            'title'=>Yii::t('backend', 'Add'), 
                            'class'=>'btn btn-success add-user'
                        ])
                ],
                [
                    'content' => $this->render('_button', [
                        'searchModel' => $searchModel
                    ])
                ],
                '{export}',
                '{toggleData}',
                [
                    'content' => Html::a('<i class="fa fa-print btn btn-default btn-lg"></i>', '#', ['id' => 'user-print'])
                ],
            ],
            'export' => [
                'fontAwesome' => true,
            ],  
            'panel' => [
                    'type' => GridView::TYPE_DEFAULT,
                    'heading' => Yii::t('backend', !isset($role) ? 'User' : $role.'s')
                ],
            'toggleDataOptions' => ['minCount' => 20],
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
    
    $(document).on('modal-success', function(event, params) {
        window.location.href=params.url;
        return false;
    });

$(document).ready(function(){
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    
    $(document).off('change', "#usersearch-showall").on('change', "#usersearch-showall", function(){
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
$("#user-print").on("click", function() {
    <?php if ($searchModel->role_name === User::ROLE_CUSTOMER) { ?>
            var url = '<?php echo Url::to(['print/user?UserSearch%5Brole_name%5D=customer']); ?>';
      <?php  } else if ($searchModel->role_name === User::ROLE_TEACHER) { ?>
            var url = '<?php echo Url::to(['print/user?UserSearch%5Brole_name%5D=teacher']); ?>';
      <?php   } ?>
        window.open(url,'_blank');
    });
</script>
