<?php

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
        'id' => 'modal-form',
        'action' => $url,
    ]); ?>

  	<div class="row">
		<div class="col-md-8">
    <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
		</div>
    </div>
    <div class="row">
		<div class="col-md-8 ">
    <?php echo $form->field($model, 'province_id')->dropDownList(ArrayHelper::map(
                            Province::find()->orderBy(['name' => SORT_ASC])->all(),
        'id',
        'name'
            )) ?>
		</div>
</div>    
    <?php ActiveForm::end(); ?>
</div>
<script>
    $(document).on('modal-success', function(event, params) {
        var url = "<?php echo Url::to(['city/index']); ?>";
        $.pjax.reload({url: url, container: "#city-listing", replace: false, timeout: 4000});
        return false;
    });
    $(document).on('modal-delete', function(event, params) {
        var url = "<?php echo Url::to(['city/index']); ?>";
+        $.pjax.reload({url: url, container: "#city-listing", replace: false, timeout: 4000});
        return false;
    });
</script>
