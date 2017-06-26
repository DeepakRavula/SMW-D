<?php

use yii\grid\GridView;
use yii\helpers\Url;
use common\models\Invoice;
use common\models\Lesson;
use common\models\PrivateLesson;

?>
<div class="grid-row-open p-15">
<?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
    <?php $columns = [
            [
                'label' => 'Student',
                'value' => function ($data) {
                    return !empty($data->course->enrolment->student->fullName) ? $data->course->enrolment->student->fullName : null;
                },
            ],
			[
                'label' => 'Phone',
                'value' => function ($data) {
                    return !empty($data->course->enrolment->student->customer->phoneNumber->number) ? $data->course->enrolment->student->customer->phoneNumber->number : null;
                },
            ],
            [
                'label' => 'Program',
                'value' => function ($data) {
                    return !empty($data->course->program->name) ? $data->course->program->name : null;
                },
            ],
            [
                'label' => 'Date',
                'value' => function ($data) {
                    $date = Yii::$app->formatter->asDate($data->date);

                    return !empty($date) ? $date : null;
                },
            ],
            [
                'label' => 'Expiry Date',
                'value' => function ($data) {
					return !empty($data->privateLesson->expiryDate) ? Yii::$app->formatter->asDate($data->privateLesson->expiryDate) : null;
                },
            ],
        ];

    ?>   
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
    'rowOptions' => function ($model, $key, $index, $grid) {
        $url = Url::to(['lesson/view', 'id' => $model->id]);

        return ['data-url' => $url];
    },
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => $columns,
    ]); ?>
	<?php yii\widgets\Pjax::end(); ?>

</div><?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
