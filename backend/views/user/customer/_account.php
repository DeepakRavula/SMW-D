<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;
use kartik\switchinput\SwitchInput;

?>

<div class="col-md-12 p-b-20">
    <div class="row">
        <div class="pull-right">
            <div class="col-md-9 p-b-20">
                <?= SwitchInput::widget([
                    'name'=>'account',
                    'pluginOptions' => [
                        'onText' => 'Customer View',
                        'offText' => 'Company View',
                        'size' => 'mini'
                    ]
                ]); ?>
            </div>
            <div class="col-md-3 p-b-20">
                <?= Html::a('<i class="fa fa-print"></i>', null, ['id' => 'account-print', 'target' => '_blank']) ?>
            </div>
        </div>
    </div>   
    <div>
    <?php
    yii\widgets\Pjax::begin([
        'id' => 'accounts-customer',
        'timeout' => 6000,
    ])

    ?>  
    <?php
    echo GridView::widget([
        'dataProvider' => $accountDataProvider,
        'summary' => false,
        'emptyText' => false,
        'tableOptions' => ['class' => 'table table-bordered m-0'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
                [
                'label' => 'Date',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->date);
                }
            ],
                [
                'headerOptions' => ['class' => 'text-left'],
                'contentOptions' => ['class' => 'text-left'],
                'label' => 'Description',
                'value' => function ($data) {
                    return $data->accountDescription;
                }
            ],
                [
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'format' => 'currency',
                'label' => 'Debit',
                'value' => function ($data) use ($isCustomerView) {
                    return !empty($data->getDebit($isCustomerView)) ? Yii::$app->formatter->asDecimal($data->getDebit($isCustomerView)) : null;
                }
            ],
                [
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'label' => 'Credit',
                'format' => 'currency',
                'value' => function ($data) use ($isCustomerView) {
                    return !empty($data->getCredit($isCustomerView)) ? abs($data->getCredit($isCustomerView)) : null;
                }
            ],
                [
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'label' => 'Balance',
                'value' => function ($data) use ($isCustomerView) {
                    return Yii::$app->formatter->asDecimal($data->getBalance($isCustomerView));
                }
            ]
        ],
    ]);

    ?>
    </div>
</div>
<?php \yii\widgets\Pjax::end(); ?>

<script>
    $('input[name="account"]').on('switchChange.bootstrapSwitch', function() {
        var accountView = $('input[name="account"]').is(":checked");
        var roleName = "<?php echo User::ROLE_CUSTOMER; ?>";
        var params = $.param({
            'UserSearch[accountView]': (accountView | 0),
            'UserSearch[role_name]': roleName
        });
        var url = "<?php echo Url::to(['user/view', 'id' => $userModel->id]); ?>&" + params;
        $.pjax.reload({ url:url, container:"#accounts-customer", replace:false, timeout: 4000 });  //Reload GridView
        return false;
    });
    
    $(document).on('click', '#account-print', function () {
        var accountView = $('input[name="account"]').is(":checked");
        var params = $.param({
            'accountView': (accountView | 0)
        });
        var url = '<?php echo Url::to(['print/account-view', 'id' => $userModel->id]); ?>&' + params;
        window.open(url, '_blank');
    });
</script>
