<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */

?>
<div class="user-search">

    <?php $form = ActiveForm::begin([
            'action' => ['report/payment'],
            'method' => 'get',
    ]);
    ?>
    <div class="container">
        <div class="row">
            <div class="col-md-3 form-group">
                <?php
                echo DateRangePicker::widget([
                    'model' => $model,
                    'attribute' => 'dateRange',
                    'convertFormat' => true,
                    'initRangeExpr' => true,
                    'pluginOptions' => [
                        'autoApply' => true,
                        'ranges' => [
                            Yii::t('kvdrp', 'Last {n} Days', ['n' => 7]) => ["moment().startOf('day').subtract(6, 'days')", 'moment()'],
                            Yii::t('kvdrp', 'Last {n} Days', ['n' => 30]) => ["moment().startOf('day').subtract(29, 'days')", 'moment()'],
                            Yii::t('kvdrp', 'This Month') => ["moment().startOf('month')", "moment().endOf('month')"],
                            Yii::t('kvdrp', 'Last Month') => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
                        ],
                        'locale' => [
                            'format' => 'M d,Y',
                        ],
                        'opens' => 'right',
                    ],

                ]);
                ?>
            </div>
            <div class="col-xs-9">
                <div class="col-md-3">
                    <div class="col-md-2 col-xs-pull-3">
                        <?php echo Html::submitButton(Yii::t('backend', 'Go'), ['class' => 'btn btn-primary btn-sm']) ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div></div>
