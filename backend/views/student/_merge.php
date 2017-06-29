<?php
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>
<div id="error-notification" style="display:none;" class="alert-danger alert fade in"></div>


<div>
    
    <?php $form = ActiveForm::begin([
            'id' => 'student-merge-form',
    ]); ?>
    <div>
    <?php
    echo GridView::widget([
        'dataProvider' => $studentModelDataProvider,
        'options' => ['class' => 'col-md-12'],
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            [
                'class' => 'yii\grid\RadioButtonColumn',
                'radioOptions' => function ($model) {
                    return [
                        'value' => $model['id'],
                        'checked' => $model['id'] == 0
                    ];
                }
            ],
            [
                'label' => 'First Name',
                'value' => function ($data) {
                    return !empty($data->first_name) ? $data->first_name : null;
                },
            ],
            [
                'label' => 'Last Name',
                'value' => function ($data) {
                    return !empty($data->last_name) ? $data->last_name : null;
                },
            ],
            [
                'label' => 'Date of Birth',
                'value' => function ($data) {
                    return !empty($data->birth_date) ? $data->birth_date : null;
                },
            ]
        ],
    ]);
	?>
	</div>
	<?php echo Html::submitButton(Yii::t('backend', 'Merge'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
	<?php ActiveForm::end(); ?>
</div>
