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
use common\models\Location;
/* @var $this yii\web\View */
/* @var $model backend\models\search\TimelineEventSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="system-event-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
	<label class="control-label">Filter by User</label>
<div class="row">   
	<div class="form-group col-md-3">
        <?php
	$locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
	echo $form->field($model, 'createdUserId')->widget(Select2::classname(), [
	    'data' => ArrayHelper::map(User::find()
                    ->joinWith('userLocation ul')
                    ->join('LEFT JOIN', 'user_profile up','up.user_id = ul.user_id')
                    ->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
                    ->andWhere(['raa.item_name' => [User::ROLE_OWNER, User::ROLE_STAFFMEMBER]])
                    ->andWhere(['ul.location_id' => $locationId])
                    ->orderBy('up.firstname')
                    ->notDeleted()
                     ->all(),
                'id', 'userProfile.fullName'),
            'pluginOptions' => [
				'multiple' => false,
				'placeholder' => 'User',
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
    </div>
	</div>

    <?php ActiveForm::end(); ?>

</div>
<script>
$(document).ready(function(){
    $("#print-timeline").on("click", function() {
        var user = $('#timelineeventsearch-createduserid').val();
        var dateRange = $('#timelineeventsearch-daterange').val();
        var params = $.param({'TimelineEventSearch[createdUserId]': user,
		'TimelineEventSearch[dateRange]': dateRange });
        var url = '<?php echo Url::to(['timeline-event/print']); ?>?' + params;
        window.open(url,'_blank');
    });
});
</script>