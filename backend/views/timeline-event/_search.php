<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use backend\models\search\TimelineEventSearch;
/* @var $this yii\web\View */
/* @var $model backend\models\search\TimelineEventSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="system-event-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
	Filter by Category
<div class="row">   
    <div class="col-md-3">
        <?php echo $form->field($model, 'category')->dropDownList(TimeLineEventSearch::categories())->label(false); ?>
    </div>
    <div class="col-md-3 form-group m-t-3">
        <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>
	</div>

    <?php ActiveForm::end(); ?>

</div>
