<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use backend\models\search\TimelineEventSearch;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\User;

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
				'placeholder' => 'Select User',
			],
        ])->label(false); ?>
    </div>  
    <div class="col-md-3 form-group m-t-3">
        <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>
	</div>

    <?php ActiveForm::end(); ?>

</div>
