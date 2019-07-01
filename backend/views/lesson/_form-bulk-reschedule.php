<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Student;
use yii\helpers\Url;
use yii\jui\DatePicker;
use kartik\select2\Select2;
use common\models\Location;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="row bulk-reschedule-form">
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        ]); ?>

    
    <?php ActiveForm::end(); ?>
</div>