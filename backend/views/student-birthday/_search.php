<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */

?>
<style>
    .e1Div{
        right: 0 !important;
        top: -59px;
    }
</style>
<div class="user-search">

    <?php
    $form = ActiveForm::begin([
            'action' => [''],
            'method' => 'get',
    ]);

    ?>
    <div class="row">
        <div class="col-md-3">
            <?php echo $form->field($model, 'month')->dropDownList(['1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April', '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August', '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December']); ?>
        </div>
        <div class="col-md-3 form-group m-t-20">
<?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
               <div class="clearfix"></div>
        </div>
    </div>

<?php ActiveForm::end(); ?>

</div>
