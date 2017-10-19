<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Province;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\City */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="city-form">

    <?php   $url = Url::to(['city/update', 'id' => $model->id]);
            if ($model->isNewRecord) {
               $url = Url::to(['city/create']);
            }
        $form = ActiveForm::begin([
        'id' => 'city-form',
        'action' => $url,
    ]); ?>

  	<div class="row">
		<div class="col-md-4">
    <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-4 ">
    <?php echo $form->field($model, 'province_id')->dropDownList(ArrayHelper::map(
                            Province::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'
            )) ?>
		</div>
</div>

<div class="row">
    <div class="col-md-12">
    <div class="pull-right">
        <?php echo Html::a('Cancel', '', ['class' => 'btn btn-default city-cancel']);?>
        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
		
    </div>
     <div class="pull-left">   
        <?php if (!$model->isNewRecord) {
                echo Html::a('Delete', ['delete', 'id' => $model->id], [
			'id' => 'city-delete-button',
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
