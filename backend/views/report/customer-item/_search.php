<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php $locationId = \Yii::$app->session->get('location_id'); ?>

    <?php $form = ActiveForm::begin([
        'action' => ['user/view'],
        'method' => 'get',
    ]); ?>
    <div class="form-group">
        
            <?php echo DateRangePicker::widget([
                'model' => $model,
                'attribute' => 'dateRange',
                'convertFormat' => true,
                'initRangeExpr' => true,
                'pluginOptions' => [
                    'autoApply' => true,
                    'ranges' => [
                        Yii::t('kvdrp', 'This Year') => ["moment().startOf('month')", "moment().endOf('month')"],
                        Yii::t('kvdrp', 'Last Year') => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
                    ],
                    'locale' => [
                        'format' => 'M d,Y',
                    ],
                    'opens' => 'right',
                    ],

                ]);
            ?>
         
            <?=
                $form->field($model, 'customerId')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(User::find()
                            ->customers($locationId)
                            ->notDeleted()
                            ->active()
                            ->all(),
                            'id', 'publicIdentity'),
                    'options' => ['placeholder' => 'Select Customer', 'class' => 'form-control'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->label(false);
            ?>
        <?php echo Html::submitButton(Yii::t('backend', 'Go'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>