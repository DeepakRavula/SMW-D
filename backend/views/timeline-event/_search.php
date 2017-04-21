<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use backend\models\search\TimelineEventSearch;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\User;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;
use common\models\Student;
/* @var $this yii\web\View */
/* @var $model backend\models\search\TimelineEventSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>
<style>
    .select2-container--krajee .select2-selection--single {
        padding: 9px 24px 6px 12px;
    }
</style>

<div class="system-event-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
	<label class="control-label">Filter by Category</label>
<div class="row">   
    <div class="col-md-2">
        <?php echo $form->field($model, 'category')->dropDownList(TimeLineEventSearch::categories())->label(false); ?>
    </div>
	<div class="form-group col-md-3">
        <?php echo $form->field($model, 'createdUserId')->widget(Select2::classname(), [
	    'data' => ArrayHelper::map(User::find()
                    ->joinWith('userLocation ul')
                    ->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
                    ->where(['raa.item_name' => [User::ROLE_OWNER, User::ROLE_STAFFMEMBER]])
                    ->andWhere(['ul.location_id' => Yii::$app->session->get('location_id')])
                    ->all(),
                'id', 'userProfile.fullName'),
            'pluginOptions' => [
				'allowClear' => true,
				'multiple' => false,
				'placeholder' => 'User',
			],
        ])->label(false); ?>
    </div>  
	<div class="form-group col-md-3">
        <?php $locationId = Yii::$app->session->get('location_id');
		echo $form->field($model, 'student')->widget(Select2::classname(), [
	    'data' => ArrayHelper::map(Student::find()
			->location($locationId)
			->active()
            ->all(), 'id', 'fullName'),
            'pluginOptions' => [
				'allowClear' => true,
				'multiple' => false,
				'placeholder' => 'Student',
			],
        ])->label(false); ?>
    </div>  
	<div class="form-group col-md-3">
		 <?php
            echo DateRangePicker::widget([
                'model' => $model,
                'attribute' => 'dateRange',
                'convertFormat' => true,
                'initRangeExpr' => true,
                'pluginOptions' => [
                    'autoApply' => true,
                    'ranges' => [
                        Yii::t('kvdrp', "Last {n} Days", ['n' => 7]) => ["moment().startOf('day').subtract(6, 'days')",
                            "moment()"],
                        Yii::t('kvdrp', "Last {n} Days", ['n' => 30]) => ["moment().startOf('day').subtract(29, 'days')",
                            "moment()"],
                        Yii::t('kvdrp', "This Month") => ["moment().startOf('month')",
                            "moment().endOf('month')"],
                        Yii::t('kvdrp', "Last Month") => ["moment().subtract(1, 'month').startOf('month')",
                            "moment().subtract(1, 'month').endOf('month')"],
                    ],
                    'locale' => [
                        'format' => 'd-m-Y'
                    ],
                    'opens' => 'left',
                ]
            ]);
            ?>
    </div>  
    <div class="col-md-3 form-group m-t-3">
        <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
    <div id="print-timeline" class="btn btn-default">
        <?= Html::a('<i class="fa fa-print"></i> Print') ?>
    </div>
    </div>
	</div>

    <?php ActiveForm::end(); ?>

</div>
<script>
$(document).ready(function(){
    $("#print-timeline").on("click", function() {
        var category = $("#timelineeventsearch-category").val();
        var user = $('#timelineeventsearch-createduserid').val();
        var dateRange = $('#timelineeventsearch-daterange').val();
        var params = $.param({ 'TimelineEventSearch[category]': category,
            'TimelineEventSearch[createdUserId]': user, 'TimelineEventSearch[dateRange]': dateRange });
        var url = '<?php echo Url::to(['timeline-event/print']); ?>?' + params;
        window.open(url,'_blank');
    });
});
</script>