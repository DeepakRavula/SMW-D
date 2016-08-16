<?php

use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="lesson-index">
<?php $form = ActiveForm::begin([
		'method' => 'post',
		'action' => 'invoice' . '?UserSearch%5Brole_name%5D=' . $searchModel->role_name . '&id=' . $userModel->id,
		]); ?>
<?php if( ! empty($unInvoicedLessonsDataProvider)): ?>
<?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
    <?php echo GridView::widget([
        'dataProvider' => $unInvoicedLessonsDataProvider,
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'columns' => [
			[
				'class' => 'yii\grid\CheckboxColumn',
							// you may configure additional properties here
			],
			[
			    'label' => 'Lesson Id',
			 	'value' => function($data){
					return ! empty($data->id) ? $data->id : null;
				}	
			],
			[
			    'label' => 'Date',
			 	'value' => function($data){
					$date = (new \DateTime($data->date))->format("m-d-Y");
					return ! empty($date) ? $date : null;
				}	
			],
            [
			    'label' => 'Program Name',
                'value' => function($data) {
                    return ! empty($data->enrolment->program->name) ? $data->enrolment->program->name : null;
                },
            ],
            [
			    'label' => 'Student Name',
                'value' => function($data) {
                    return ! empty($data->enrolment->student->fullName) ? $data->enrolment->student->fullName : null;
                },
            ],
        ],
    ]); ?>
 <?php yii\widgets\Pjax::end(); ?>
	<?php echo $form->field($model, 'internal_notes')->textarea() ?>
	<?php echo $form->field($model, 'notes')->label('Printed Notes')->textarea() ?>
	<div class="form-group">
        <?php echo Html::submitButton(Yii::t('backend', 'Create'), ['class' => 'btn btn-success', 'name' => 'signup-button']) ?>
    </div>
<?php endif;?>
	<?php ActiveForm::end(); ?>
</div>