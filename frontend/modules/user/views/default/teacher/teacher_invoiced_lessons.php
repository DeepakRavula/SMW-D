<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;
use common\models\InvoiceLineItem;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

?>
<?php LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Invoiced Lessons',
    'withBorder' => true,
]) ?>
<div class="col-md-12">
    <?php $form = ActiveForm::begin([
        'id' => 'time-voucher-search-form',
    ]); ?>
    
    <div class="row">
        <div class="col-md-3 form-group">
            <?= DateRangePicker::widget([
                'model' => $searchModel,
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
            ]); ?>
        </div>
        <div class="col-md-1 form-group">
            <?= Html::submitButton(Yii::t('backend', 'Search'), ['id' => 'search', 'class' => 'btn btn-primary']) ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <?php ActiveForm::end(); ?>
    <?php LteBox::end() ?>
</div>
<?= $this->render('_cost-time-voucher-content', [
    'model' => $model,
    'searchModel' => $searchModel,
    'invoicedLessonsDataProvider' => $invoicedLessonsDataProvider,
]); ?>
