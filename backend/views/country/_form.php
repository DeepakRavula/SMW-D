<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Country */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="country-form">

    <?php   $url = Url::to(['country/update', 'id' => $model->id]);
            if ($model->isNewRecord) {
               $url = Url::to(['country/create']);
            }
        $form = ActiveForm::begin([
        'id' => 'country-form',
        'action' => $url,
    ]); ?>

    <div class="row">
		<div class="col-md-4">
    <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
		</div>
	</div>
<div class="row">
    <div class="col-md-12">
    <div class="pull-right">
         <?php
        echo Html::a('Cancel', '#', ['class' => 'btn btn-default country-cancel']);?>
        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
    </div>
    <div class="pull-left">
       <?php if (!$model->isNewRecord) {
            echo Html::a('Delete', ['delete', 'id' => $model->id], [
                'id' => 'province-delete-button',
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ]
            ]);
        }
        ?>
    </div>
</div>
</div>

    <?php ActiveForm::end(); ?>

</div>
