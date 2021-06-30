<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model common\models\ClassroomUnavailability */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div id="classroom-unavailability-validation" style="display: none;" class="alert-danger alert fade in"></div>
<div class="classroom-unavailability-form">

    <?php
    $form = ActiveForm::begin([
            'id' => 'classroom-unavailability-form',
    ]);
    ?>
    <div class="row">
        <div class="col-md-5">
            <div class="form-group">
                <label>Date Range</label>
                <?php
                echo DateRangePicker::widget([
                    'model' => $model,
                    'attribute' => 'dateRange',
                    'convertFormat' => true,
                    'initRangeExpr' => true,
                    'options' => [
                        'class' => 'form-control',
                        'readOnly' => true
                    ],
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

        <div class="col-lg-10">
<?php echo $form->field($model, 'reason')->textarea(['rows' => 6]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
        <?php echo Html::a('Cancel', '#', ['class' => 'btn btn-default classroom-unavailability-cancel-button']); ?>
        <?php echo Html::submitButton('Save', ['class' => 'btn btn-info']) ?>
            </div>
            <div class="pull-left">
        <?php if (!$model->isNewRecord) {
                    echo Html::a('Delete', ['delete', 'id' => $model->id], [
                        'id' => 'classroom-unavailability-delete-button',
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ]
                    ]);
                }
                ?>
            </div>
        </div></div>
<?php ActiveForm::end(); ?>
</div>
