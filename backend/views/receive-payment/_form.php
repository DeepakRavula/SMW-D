<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use common\models\PaymentMethod;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentMethods */
/* @var $form yii\bootstrap\ActiveForm */
?>

<?php 
$query = PaymentMethod::find()->andWhere(['active'=> PaymentMethod::STATUS_ACTIVE]);
if (!($model->isCreditUsed() || $model->isCreditApplied())) : 
    $query->andWhere(['displayed' => 1]); 
endif; 
$query->orderBy(['sortOrder' => SORT_ASC]);
$paymentMethods = $query->all();  ?>

<div class="receive-payment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'date')->widget(DatePicker::classname(), [
        'value'  => Yii::$app->formatter->asDate($model->date),
        'dateFormat' => 'php:M d, Y',
        'options' => [
            'class' => 'form-control'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'yearRange' => '1500:3000',
            'changeYear' => true
        ]
    ])->label('Date'); ?>

    <?= $form->field($model, 'payment_method_id')->dropDownList(ArrayHelper::map($paymentMethods, 'id', 'name'), 
        ['disabled' => $model->isCreditUsed() || $model->isCreditApplied()]);
    ?>
    <?php ActiveForm::end(); ?>

</div>
