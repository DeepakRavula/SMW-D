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
            'id' => 'modal-form',
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
</div>  

<?php ActiveForm::end(); ?>
<script>
    $(document).on('modal-success', function(event, params) {
        var url = "<?php echo Url::to(['province/index']); ?>";
        $.pjax.reload({url: url, container: "#province-listing", replace: false, timeout: 4000});
        return false;
    });
    $(document).on('modal-delete', function(event, params) {
        var url = "<?php echo Url::to(['province/index']); ?>";
+        $.pjax.reload({url: url, container: "#province-listing", replace: false, timeout: 4000});
        return false;
    });
</script>

