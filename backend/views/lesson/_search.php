<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use backend\models\search\LessonSearch;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'invoiceStatus')->dropDownList(LessonSearch::invoiceStatuses());?>

    <?php echo $form->field($model, 'lessonStatus')->dropDownList(LessonSearch::lessonStatuses());?>

    <div class="form-group">
        <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
