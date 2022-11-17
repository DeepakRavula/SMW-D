<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use backend\models\search\LessonSearch;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="user-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">   
    <div class="col-md-3">
        <?php echo $form->field($model, 'lessonStatus')->dropDownList(LessonSearch::lessonStatuses())->label('Status'); ?>
    </div>
    
    <div class="col-md-3">
         <div class="form-group">
			 <label>Date Range</label>
            <?php echo DateRangePicker::widget([
            'model' => $model,
            'attribute' => 'dateRange',
            'convertFormat' => true,
            'initRangeExpr' => true,
            'pluginOptions' => [
                'autoApply' => true,
                'ranges' => [
                    Yii::t('kvdrp', 'Today') => ["moment().startOf('day')", "moment()"],
                    Yii::t('kvdrp', 'Tomorrow') => ["moment().startOf('day').add(1,'days')", "moment().endOf('day').add(1,'days')"],
                    Yii::t('kvdrp', 'Next {n} Days', ['n' => 7]) => ["moment().startOf('day')", "moment().endOf('day').add(6, 'days')"],
                    Yii::t('kvdrp', 'Next {n} Days', ['n' => 30]) => ["moment().startOf('day')", "moment().endOf('day').add(29, 'days')"],
                ],
                'locale' => [
                    'format' => 'M d,Y',
                ],
                'opens' => 'right',
                ],

            ]);
           ?>
        </div>
    </div>
	<?php echo $form->field($model, 'type')->hiddenInput()->label(false); ?>
	<?php echo Html::submitButton(Yii::t('backend', 'Go'), ['class' => 'btn btn-primary btn-sm', 'style' => 'margin-bottom:-27px']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
