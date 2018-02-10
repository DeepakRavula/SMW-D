<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Country;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Province */
/* @var $form yii\bootstrap\ActiveForm */

?>

<div class="province-form">

    <?php
    $url = Url::to(['province/update', 'id' => $model->id]);
    if ($model->isNewRecord) {
        $url = Url::to(['province/create']);
    }
    $form = ActiveForm::begin([
            'id' => 'province-form',
            'action' => $url,
    ]);

    ?>


    <div class="row">
        <div class="col-md-8">
<?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <?php echo $form->field($model, 'tax_rate')->textInput() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <?php
            echo $form->field($model, 'country_id')->dropDownList(ArrayHelper::map(
                    Country::find()->orderBy(['name' => SORT_ASC])->all(),
        'id',
        'name'
            ))->label('Country')
            ?>
        </div>
    </div>
<div class="row">
    <div class="col-md-12">
    <div class="pull-right">
        <?php echo Html::a('Cancel', '#', ['class' => 'province-cancel btn btn-default']);?>
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
</div>  

<?php ActiveForm::end(); ?>

</div>
